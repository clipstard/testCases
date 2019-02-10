<?php


class binarySearch{
    /**
     * @param array $array
     * @param $needle
     * @return int
     */
    public static function action(array $array, $needle)
    {
        $itemsCount = count($array);
        if ($itemsCount === 0) return -1;
        $low = 0;
        $high = $itemsCount - 1;

        while ($low <= $high) {

            $mid = (int)(floor(($low + $high) / 2));

            if($array[$mid] == $needle) {
                return $mid;
            }

            if ($needle < $array[$mid]) {
                $high = $mid -1;
            }
            else {
                $low = $mid + 1;
            }
        }
        return -1;
    }
}