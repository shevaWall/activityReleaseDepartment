<?php

namespace App\Http\Controllers;

use App\Models\Composit;
use App\Models\CountPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountPdfController extends Controller
{
    /**
     * @var string - путь до файла PDF
     */
    private $pathToPdf;

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
    private static $tolerance = 10;

    private static $a_ISO_format = array([
        'A4'    => [210, 297],
        'A4x3'  => [297, 630],
        'A4x4'  => [297, 841],
        'A4x5'  => [297, 1051],
        'A4x6'  => [297, 1261],
        'A4x7'  => [297, 1471],
        'A4x8'  => [297, 1682],
        'A4x9'  => [297, 1982],

        'A3'    => [297, 420],
        'A3x3'  => [420, 891],
        'A3x4'  => [420, 1189],
        'A3x5'  => [420, 1486],
        'A3x6'  => [420, 1783],
        'A3x7'  => [420, 2080],

        'A2'    => [420, 594],
        'A2x3'  => [594, 1261],
        'A2x4'  => [594, 1682],
        'A2x5'  => [594, 2102],

        'A1'    => [594, 841],
        'A1x2'  => [841, 1783],
        'A1x3'  => [841, 2378],

        'A0'    => [841, 1198],
        'A0x2'  => [118, 1682],
        'A0x3'  => [118, 2523],
    ]);


    /**
     * выплёвывает массив всех размеров страниц в ПДФ в pts
     * @return array
     * unused
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    private function shellPdfExec(): bool
    {
        if ($this->pathToPdf) {
            // Через pdfinfo получаем форматы
            $shell = shell_exec("pdfinfo -f 1 -l 167 $this->pathToPdf | grep 'Page.*size:'");
            preg_match_all('/([0-9]{0,5}\.?[0-9]{0,3}) x ([0-9]{0,5}\.?[0-9]{0,3})/', $shell, $res);
            $this->sizes = $res;
            $this->widths = $res[1];
            $this->heights = $res[2];

            // через ghostscript получаем цветность
            $shell2 = shell_exec("gs -dSAFER -dNOPAUSE -dBATCH -o- -sDEVICE=inkcov $this->pathToPdf | grep -E '([0-9]{1}\.[0-9]{5})'");
            preg_match_all('/([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})/', $shell2, $this->a_CMYKPages);

            $this->convert2mm();
            return true;
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
     * Проверяет, является ли страница цветной
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
    public function ajaxLoadFile(Request $request, int $composit_id)
    {
        if ($request->file('pdf')) {
            $pdf = Storage::putFile('pdfs', $request->file('pdf'));
            $this->pathToPdf = Storage::path($pdf);
            if ($this->shellPdfExec()) {
                CountPdf::updateOrCreate([
                    'composit_id' => $composit_id
                ], [
                        'formats' => json_encode($this->countFormats())
                    ]
                );
            }
            Storage::delete($pdf);
        }
    }

    public function ajaxGetCountedPdf(int $composit_id)
    {
        $composit = Composit::where('id', $composit_id)->with('formats')->get();
        return view('composit.formatsTable')
            ->with('composit', $composit[0]);
    }

    public function test(){
        $this->pathToPdf = "/var/www/storage/app/public/test.pdf";
        $this->shellPdfExec();


        dump($this->countFormats());
    }
}
