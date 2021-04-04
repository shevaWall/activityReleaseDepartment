<?php

namespace App\Http\Controllers;

class ShevawallPdf
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


    public function __construct(string $file)
    {
        (file_exists($file)) ? $this->pathToPdf = $file : false;

        $this->shellPdfExec();
    }

    /**
     * выплёвывает массив всех размеров страниц в ПДФ в pts
     * @return array
     */
    public function getSizes(): array
    {
        return $this->sizes;
    }

    private function shellPdfExec()
    {
        if ($this->pathToPdf) {
            $shell = shell_exec("pdfinfo -f 1 -l 167 $this->pathToPdf | grep 'Page.*size:'");
            preg_match_all('/([0-9]{0,5}\.?[0-9]{0,3}) x ([0-9]{0,5}\.?[0-9]{0,3})/', $shell, $res);
            $this->sizes = $res;
            $this->widths = $res[1];
            $this->heights = $res[2];

            $shell2 = shell_exec("gs -dSAFER -dNOPAUSE -dBATCH -o- -sDEVICE=inkcov $this->pathToPdf | grep -E '([0-9]{1}\.[0-9]{5})'");
            preg_match_all('/([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})  ([0-9]{1}\.[0-9]{5})/', $shell2, $this->a_CMYKPages);
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


    public function countFormats(): array
    {
        $countPages = count($this->sizes[0]);

        for($page=0; $page<$countPages; $page++){
            $WaH = "$this->widths[$page] x $this->heights[$page]";

//            Если такой формат бумаги уже есть в массиве
            if(array_key_exists($WaH, $this->a_countsFormats)){
//                Увеличиваем общий счётчик формата бумаги
                $this->a_countsFormats[$WaH]['count'] = ++ $this->a_countsFormats[$WaH]['count'];

//                Проверяем, цветная ли страница
                if($this->chekOnColored($page)){
//                    Проверяем существование такого ключа по текущему формату, если нет, то инициализируем в 1
                    (array_key_exists('BW', $this->a_countsFormats[$WaH])) ?
                        $this->a_countsFormats[$WaH]['BW'] = ++ $this->a_countsFormats[$WaH]['BW'] :
                        $this->a_countsFormats[$WaH]['BW'] = 1;
                }else{
                    (array_key_exists('Colored', $this->a_countsFormats[$WaH])) ?
                        $this->a_countsFormats[$WaH]['Colored'] = ++ $this->a_countsFormats[$WaH]['Colored'] :
                        $this->a_countsFormats[$WaH]['Colored'] = 1;
                }
            }else{
//                Если такого формата бумаги в массиве нет, то инициализируем его
                $this->a_countsFormats[$WaH]['count'] = 1;
//                Естественно с определением цветности бумаги
                ($this->chekOnColored($page)) ?
                    $this->a_countsFormats[$WaH]['Colored'] = 1 :
                    $this->a_countsFormats[$WaH]['BW'] = 1;
            }
        }

        return $this->a_countsFormats;
    }

    /**
     * Проверяет, является ли страница цветной
     * @param int $page - номер страницы
     * @return bool
     */
    private function chekOnColored(int $page): bool
    {
        if($this->a_CMYKPages[1][$page] == '0.00000' && $this->a_CMYKPages[2][$page] == '0.00000' && $this->a_CMYKPages[3][$page] == '0.00000'){
            return false;
        }else{
            return true;
        }
    }
}
