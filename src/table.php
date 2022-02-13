<?php
namespace App;

class Table{

    const  SORT_KEY ="sort";
    const DIR_KEY = "dir";

    public static function sort (string $sortkey, string $label, array $data): string
    {
        $sort = $data[self::SORT_KEY] ?? null;
        $direction = $data[self::DIR_KEY] ?? null;
        $icon = "";
        if ($sort === $sortkey){
            $icon = $direction === "asc" ? "^" : "V";
        }
        $url = URL::withParams($data, [
            "sort" => $sortkey,
            'dir' => $direction === "asc" ? "desc" : "asc"
        ]);
        return <<<HTML
        <a href="?$url">$label $icon</a>
        
HTML;

    }
}
