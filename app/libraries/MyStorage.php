<?php

use Carbon\Carbon;

class MyStorage {

    public static function getTargetDir($base_dir) {
        $now = Carbon::now();
        $year = $now->year();
        $month = jdmonthname($now->month, CAL_MONTH_GREGORIAN_SHORT);

        return $base_dir . '/' . $year . '/' . $month;
    }

    public static function generateUnique($filesystem, $dir, $name, $ext, $i = 0) {
        $append = '';
        if ($i) $append = '-' . $i;

        $filename = $dir . '/' . $name . $append . '.' . $ext;
        if ($filesystem->exists($filename)) {
            $i++;
            return static::generateUnique($filesystem, $dir, $name, $ext, $i);
        } else {
            return $filename;
        }
    }

    public static function getUniqueFileName($filesystem, $file, $target_dir) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $name = pathinfo($file, PATHINFO_FILENAME);
        $name = preg_replace('/[\W]/', '-', $name);

        return static::generateUnique($filesystem, $target_dir, $name, $ext, 0);
    }
} 