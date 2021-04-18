<?php

namespace App\Http\Controllers;

use App\Models\Composit;
use App\Models\CountPdf;
use App\Models\PrintableObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\Types\Mixed_;

class CountPdfController extends Controller
{
    /**
     * @var string - абсолютный путь до файла PDF. Нужен для работы с gs и pdfinfo
     */
    private $pathToPdf;

    /**
     * @var string - путь для Storage. Нужен для работы Storage
     */
    private $pdf;

    /**
     * @var array - массив со всеми исходными размерами (включая widths and heights)
     */
    private $sizes = [];

    /**
     * @var array - массив только с шириной каждого листа
     */
    private $widths = [];

    /**
     * @var array - массив только с высотой каждого листа
     */
    private $heights = [];

    /**
     * @var array - массив с подсчитанными по форматам листами
     */
    private $a_countsFormats = [];

    private $a_CMYKPages = [];

    /**
     * @var float - коэффициент для преобразования pts(типографический пункт) в миллиметры
     */
    private static $coef = 2.835;

    /**
     * @var int - погрешность +/- формата листа. Т.к. люди не умеют PDF'ить, то и появляется погрешность вида
     * 297х210, 296х210, 297х209, 297х211 и т.д.
     */
    private static $tolerance = 50;

    private static $a_ISO_format = array([
        'A4'        => [210, 297],
        'A4x3'      => [297, 630],
        'A4x4'      => [297, 841],
        'A4x5'      => [297, 1051],
        'A4x6'      => [297, 1261],
        'A4x7'      => [297, 1471],
        'A4x8'      => [297, 1682],
        'A4x9'      => [297, 1982],

        'A3'        => [297, 420],
        'A3x3'      => [420, 891],
        'A3x4'      => [420, 1189],
        'A3x5'      => [420, 1486],
        'A3x6'      => [420, 1783],
        'A3x7'      => [420, 2080],
        'A3x8'      => [420, 2376],
        'A3x9'      => [420, 2673],

        'A2'        => [420, 594],
        'A2x3'      => [594, 1261],
        'A2x4'      => [594, 1682],
        'A2x5'      => [594, 2102],
        'A2x6'      => [594, 2520],
        'A2x7'      => [594, 2940],
        'A2x8'      => [594, 3360],
        'A2x9'      => [594, 3780],

        'A1'        => [594, 841],
        'A1x1,5'    => [841, 891],
        'A1x2,5'    => [841, 1485],
        'A1x3'      => [841, 1782],
        'A1x4'      => [841, 2376],
        'A1x5'      => [841, 2970],
        'A1x6'      => [841, 3564],
        'A1x7'      => [841, 4158],
        'A1x8'      => [841, 4752],
        'A1x9'      => [841, 5346],

        'A0'        => [841, 1198],
        'A0x1,5'    => [1189, 1262],
        'A0x2'      => [1189, 1682],
        'A0x2,5'    => [1189, 2103],
        'A0x3'      => [1189, 2523],
        'A0x4'      => [1189, 3364],
        'A0x5'      => [1189, 4205],
        'A0x6'      => [1189, 5046],
        'A0x7'      => [1189, 5887],
        'A0x8'      => [1189, 6728],
        'A0x9'      => [1189, 7569],
    ]);


    /**
     * выплёвывает массив всех размеров страниц в ПДФ в pts (unused)
     * @return array
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    /**
     * через shell выполняем запросы pdfinfo и ghostscript
     * @return bool
     */
    private function shellPdfExec()
    {
        if ($this->pathToPdf) {
            // Через pdfinfo получаем форматы
            $shell = shell_exec("pdfinfo -f 1 -l -1 $this->pathToPdf | grep 'Page.*size:'");
            preg_match_all('/([0-9]{0,5}\.?[0-9]{0,3}) x ([0-9]{0,5}\.?[0-9]{0,3})/', $shell, $res);
            $this->sizes = $res;
            $this->widths = $res[1];
            $this->heights = $res[2];

            // через ghostscript получаем цветность
            $shell2 = shell_exec("gs -dSAFER -dNOPAUSE -dBATCH -o- -sDEVICE=inkcov $this->pathToPdf | grep -E '([0-9]{1}\.[0-9]{5})'");

            // удаляем файл, дальше он нам не нужен
            Storage::delete($this->pdf);

            if($shell2 !== null){
                preg_match_all('/([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})/', $shell2, $this->a_CMYKPages);
                $this->convert2mm();
                return true;
            }else{
                return false;
            }
        } else {
            echo 'такого файла не существует';
            return false;
        }
    }

    /**
     * Преобразует pts в mm
     */
    public function convert2mm()
    {
        foreach ($this->widths as $k => $width) {
            $this->widths[$k] = intval($width / static::$coef);
        }
        foreach ($this->heights as $k => $height) {
            $this->heights[$k] = intval($height / static::$coef);
        }
    }

    private function WaHorHaW($size, $page)
    {
        // Увеличиваем общий счётчик формата бумаги
        $this->a_countsFormats[$size]['count'] = ++$this->a_countsFormats[$size]['count'];

        // Проверяем, цветная ли страница
        if ($this->checkOnColored($page)) {
            // Проверяем существование такого ключа по текущему формату, если нет, то инициализируем в 1
            (array_key_exists('BW', $this->a_countsFormats[$size])) ?
                $this->a_countsFormats[$size]['BW'] = ++$this->a_countsFormats[$size]['BW'] :
                $this->a_countsFormats[$size]['BW'] = 1;
        } else {
            (array_key_exists('Colored', $this->a_countsFormats[$size])) ?
                $this->a_countsFormats[$size]['Colored'] = ++$this->a_countsFormats[$size]['Colored'] :
                $this->a_countsFormats[$size]['Colored'] = 1;
        }
    }

    /**
     * Возвращает посчитанный массив по форматам и цветной/не цветной
     * @return array
     */
    public function countFormats(): array
    {
//        todo: сделать сортировку от меньшего к большему
        $countPages = count($this->sizes[0]);

        for ($page = 0; $page < $countPages; $page++) {
            $w = $this->widths[$page];
            $h = $this->heights[$page];
            if($w < $h){
                $smaller = $w;
                $bigger  = $h;
            }else{
                $smaller = $h;
                $bigger  = $w;
            }

            foreach(static::$a_ISO_format as $a_formats){
                // пробегаем по всему циклу форматов, если встречается такой формат - добавляем его
                foreach($a_formats as $k=>$v){
                    if($v[0]-self::$tolerance <= $smaller && $smaller <= $v[0]+self::$tolerance){
                        if($v[1]-self::$tolerance <= $bigger && $bigger <= $v[1]+self::$tolerance){
                            // Если такой формат бумаги уже есть в массиве
                            if (array_key_exists($k, $this->a_countsFormats)) {
                                $this->WaHorHaW($k, $page);
                            } else {
                                // Если такого формата бумаги в массиве нет, то инициализируем его
                                $this->a_countsFormats[$k]['count'] = 1;
                                // Естественно с определением цветности бумаги
                                ($this->checkOnColored($page)) ?
                                    $this->a_countsFormats[$k]['BW'] = 1:
                                    $this->a_countsFormats[$k]['Colored'] = 1;
                            }
                            break 2;
                        }
                    }
                }
                // если неформат - добавляем кастомный
                $this->a_countsFormats[$smaller. " x ". $bigger]['count'] = 1;
                // Естественно с определением цветности бумаги
                ($this->checkOnColored($page)) ?
                    $this->a_countsFormats[$smaller. " x ". $bigger]['BW'] = 1:
                    $this->a_countsFormats[$smaller. " x ". $bigger]['Colored'] = 1;
            }
        }

        return $this->a_countsFormats;
    }

    /**
     * Проверяет, является ли страница BW
     * @param int $page - номер страницы
     * @return bool
     */
    private function checkOnColored(int $page): bool
    {
        if ($this->a_CMYKPages[1][$page] == '0.00000' &&
            $this->a_CMYKPages[2][$page] == '0.00000' &&
            $this->a_CMYKPages[3][$page] == '0.00000') {
            return true;
        } else {
            return false;
        }
    }


//    todo: свой request для обработки

    /**
     * загружаем файл к себе, для его обработки. После обработки удаляём
     * @param Request $request
     * @param int $composit_id
     */
    public function ajaxLoadFile(Request $request, int $composit_id)
    {
        if ($request->file('pdf')) {
            $pdf = Storage::putFile('pdfs', $request->file('pdf'));
            $this->pdf = $pdf;
            $this->pathToPdf = Storage::path($this->pdf);

            if ($this->shellPdfExec() === true) {
                CountPdf::updateOrCreate([
                    'composit_id' => $composit_id
                ], [
                        'formats' => json_encode($this->countFormats())
                    ]
                );
            }else{
                $data['error_code'] = 406;
                return response()->view('errors.bad_pdf', $data, 406);
            }
        }
    }

    public function ajaxGetCountedPdf(int $composit_id)
    {
        $composit = Composit::where('id', $composit_id)->with('formats')->get();
        return view('composit.formatsTable')
            ->with('composit', $composit[0]);
    }

    /**
     * очищает весь список подсчитанных форматов
     * @param int $object_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll(int $object_id): \Illuminate\Http\RedirectResponse
    {
        $object = PrintableObject::where('id', $object_id)
            ->with('countPdf')
            ->get();

        foreach($object[0]->countPdf as $k => $v){
            CountPdf::destroy($v->id);
        }

        return redirect()->back();
    }

    /**
     * очищает список подсчитанных форматов у определенного раздела (состава)
     * @param int $composit_id
     */
    public function ajaxDropCounted(int $composit_id){
        CountPdf::where('composit_id', $composit_id)->delete();
    }

    public function test(){
//        $this->pathToPdf = "/var/www/storage/app/public/II_bad_copy.pdf";
        $pdf = Storage::putFile('pdfs', '/var/www/storage/app/public/II_bad.pdf');
        $this->pdf = $pdf;
        $this->pathToPdf = Storage::path($this->pdf);
        $this->shellPdfExec();

        dump($this->countFormats());
        dump(Storage::delete($this->pdf));
    }
}
