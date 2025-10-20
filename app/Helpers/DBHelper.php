<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DBHelper
{

    public static function dbField($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("$table.$columnName") . " AS $rename";
        }
        return DB::raw("$table.$columnName");
    }

    public static function dbConcat($tableName1, $columnName1, $tableName2, $columnName2, $rename = "", $seperator = '')
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            if (!empty($seperator)) {
                return DB::raw("CONCAT($table1.$columnName1, $seperator,$table2.$columnName2) AS $rename");
            } else {
                return DB::raw("CONCAT($table1.$columnName1, ' ',$table2.$columnName2) AS $rename");
            }
        } else {
            return DB::raw("CONCAT($table1.$columnName1, ' ',$table2.$columnName2)");
        }
    }

    public static function dbConcatPrefixWithFixed($tableName1, $columnName1, $fixed, $rename = "", $seperator = '')
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            if (!empty($seperator)) {
                return DB::raw("CONCAT('$fixed', '$seperator', $table1.$columnName1) AS $rename");
            } else {
                return DB::raw("CONCAT('$fixed', $table1.$columnName1) AS $rename");
            }
        } else {
            return DB::raw("CONCAT('$fixed', $table1.$columnName1)");
        }
    }

    public static function dbConcatPrefixWithIfNull($tableName1, $columnName1, $tableName2, $columnName2, $tableName3, $columnName3, $rename = "", $seperator = '')
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        $table3 = DB::getTablePrefix() . $tableName3;

        if (!empty($rename)) {
            if (!empty($seperator)) {
                return DB::raw("CONCAT($table1.$columnName1, '$seperator', IFNULL($table2.$columnName2, $table3.$columnName3)) AS $rename");
            } else {
                return DB::raw("CONCAT($table1.$columnName1, IFNULL($table2.$columnName2, $table3.$columnName3)) AS $rename");
            }
        } else {
            return DB::raw("CONCAT($table1.$columnName1, IFNULL($table2.$columnName2, $table3.$columnName3))");
        }
    }

    public static function dbDate($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("DATE($table.$columnName) AS $rename");
        }
        return DB::raw("DATE($table.$columnName)");
    }

    public static function dbUpper($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("UPPER($table.$columnName) AS $rename");
        }
        return DB::raw("UPPER($table.$columnName)");
    }

    public static function dbTime($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("TIME($table.$columnName) AS $rename");
        }
        return DB::raw("TIME($table.$columnName)");
    }

    public static function dbIfNull($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("IFNULL($table.$columnName, '$defaultVal') AS $rename");
        }
        return DB::raw("IFNULL($table.$columnName, '$defaultVal')");
    }

    public static function dbIfNullWithPlus($tableName1, $columnName1, $tableName2, $columnName2, $defaultVal = "", $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("(IFNULL($table1.$columnName1, '$defaultVal') + IFNULL($table2.$columnName2, '$defaultVal')) AS $rename");
        }
        return DB::raw("(IFNULL($table1.$columnName1, '$defaultVal') + IFNULL($table2.$columnName2, '$defaultVal'))");
    }

    public static function dbCoalesce($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("COALESCE($table.$columnName, '$defaultVal') AS $rename");
        }
        return DB::raw("COALESCE($table.$columnName, '$defaultVal')");
    }

    public static function dbGroupConcat($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("GROUP_CONCAT($table.$columnName) AS $rename");
        }
        return DB::raw("GROUP_CONCAT($table.$columnName)");
    }

    public static function dbDistinctGroupConcat($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("GROUP_CONCAT(DISTINCT $table.$columnName) AS $rename");
        }
        return DB::raw("GROUP_CONCAT(DISTINCT $table.$columnName)");
    }

    public static function dbIfNullWithSum($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("IFNULL(SUM($table.$columnName), '$defaultVal') AS $rename");
        }
        return DB::raw("IFNULL(SUM($table.$columnName), '$defaultVal')");
    }

    public static function dbSumWithIfNull($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("SUM(IFNULL($table.$columnName, '$defaultVal')) AS $rename");
        }
        return DB::raw("SUM(IFNULL($table.$columnName, '$defaultVal'))");
    }

    public static function dbCount($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("COUNT($table.$columnName) AS $rename");
        }
        return DB::raw("COUNT($table.$columnName)");
    }

    public static function dbDistinctCount($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("COUNT(DISTINCT $table.$columnName) AS $rename");
        }
        return DB::raw("COUNT(DISTINCT $table.$columnName)");
    }

    public static function dbMax($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("MAX($table.$columnName) AS $rename");
        }
        return DB::raw("MAX($table.$columnName)");
    }

    public static function dbMin($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("MIN($table.$columnName) AS $rename");
        }
        return DB::raw("MIN($table.$columnName)");
    }

    public static function dbSum($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("SUM($table.$columnName) AS $rename");
        }
        return DB::raw("SUM($table.$columnName)");
    }

    public static function dbSumTwoFields($tableName, $columnName, $second, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("SUM($table.$columnName - $second) AS $rename");
        }
        return DB::raw("SUM($table.$columnName - $second)");
    }

    public static function dbSumTwoFieldsWithTwoTables($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("SUM($table1.$columnName1 - $table2.$columnName2) AS $rename");
        }
        return DB::raw("SUM($table1.$columnName1 - $table2.$columnName2)");
    }

    public static function dbIfNullSumTwoFieldsWithTwoTables($tableName1, $columnName1, $tableName2, $columnName2, $defaultVal = "", $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IFNULL(SUM($table1.$columnName1 - $table2.$columnName2), $defaultVal) AS $rename");
        }
        return DB::raw("IFNULL(SUM($table1.$columnName1 - $table2.$columnName2), $defaultVal)");
    }

    public static function dbSubCountTwoFieldsWithTwoTables($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IFNULL($table2.$columnName2, 0) - IFNULL(COUNT($table1.$columnName1), 0) AS $rename");
        }
        return DB::raw("IFNULL($table2.$columnName2, 0) - IFNULL(COUNT($table1.$columnName1), 0)");
    }

    public static function dbIfNullWithColumnsTwo($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IFNULL($table1.$columnName1, $table2.$columnName2) AS $rename");
        }
        return DB::raw("IFNULL($table1.$columnName1, $table2.$columnName2)");
    }

    public static function dbGraterThanWithColumnsTwo($tableName1, $columnName1, $tableName2, $columnName2, $tableName3, $columnName3, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        $table3 = DB::getTablePrefix() . $tableName3;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 > 0, $table2.$columnName2, $table3.$columnName3) AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 > 0, $table2.$columnName2, $table3.$columnName3)");
    }

    public static function dbGraterThanWithColumnsTwoAndFixed($tableName1, $columnName1, $tableName2, $columnName2, $fixed2, $tableName3, $columnName3, $fixed3, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        $table3 = DB::getTablePrefix() . $tableName3;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 > 0, CONCAT('$fixed2', $table2.$columnName2), CONCAT('$fixed3', $table3.$columnName3)) AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 > 0, CONCAT('$fixed2', $table2.$columnName2), CONCAT('$fixed3', $table3.$columnName3))");
    }

    public static function dbGraterThanWithColumnsThreeAndFixed($conTableName1, $conditionColumnName1, $tableName1, $columnName1, $fixed1,  $conTableName2, $conditionColumnName2, $tableName2, $columnName2, $fixed2, $conTableName3, $conditionColumnName3, $tableName3, $columnName3, $fixed3, $rename = "")
    {
        $prefix = DB::getTablePrefix();
        $ctable1 = $prefix . $conTableName1;
        $ctable2 = $prefix . $conTableName2;
        $ctable3 = $prefix . $conTableName3;
        $table1 = $prefix . $tableName1;
        $table2 = $prefix . $tableName2;
        $table3 = $prefix . $tableName3;
        if (!empty($rename)) {
            return DB::raw("IF($ctable1.$conditionColumnName1 > 0, CONCAT('$fixed1', $table1.$columnName1), IF($ctable2.$conditionColumnName2 > 0, CONCAT('$fixed2', $table2.$columnName2), IF($ctable3.$conditionColumnName3 > 0, CONCAT('$fixed3', $table3.$columnName3), ''))) AS $rename");
        }
        return DB::raw("IF($ctable1.$conditionColumnName1 > 0, CONCAT('$fixed1', $table1.$columnName1), IF($ctable2.$conditionColumnName2 > 0, CONCAT('$fixed2', $table2.$columnName2), IF($ctable3.$conditionColumnName3 > 0, CONCAT('$fixed3', $table3.$columnName3), '')))");
    }

    public static function dbGraterThanWithOrColumnsTwo($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 > 0 OR $table2.$columnName2 > 0, 1, 0) AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 > 0 OR $table2.$columnName2 > 0, 1, 0)");
    }

    public static function dbGraterThanWithColumnsTwoConcat($tableName1, $columnName1, $tableName2, $columnName2, $columnName3, $emptyColumn1,  $tableName3, $columnName4, $columnName5, $emptyColumn2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        $table3 = DB::getTablePrefix() . $tableName3;
        $f2 = DB::raw("TRIM(CONCAT($table2.$columnName2, ' ',$table2.$columnName3))");
        $f3 = DB::raw("TRIM(CONCAT($table3.$columnName4, ' ',$table3.$columnName5))");

        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 > 0, IF($f2 != '', $f2, $table2.$emptyColumn1), IF($f3 != '', $f3, $table3.$emptyColumn2)) AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 > 0, IF($f2 != '', $f2, $table2.$emptyColumn1), IF($f3 != '', $f3, $table3.$emptyColumn2))");
    }

    public static function dbGraterThanWithColumnAndFixed($tableName1, $columnName1, $tableName2, $columnName2, $fixed, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 > 0, $table2.$columnName2, '$fixed') AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 > 0, $table2.$columnName2, '$fixed')");
    }

    public static function dbSubValueWithGraterThan($tableName1, $columnName1, $fixed, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 - $fixed > 0, $table1.$columnName1 - $fixed, '0') AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 - $fixed > 0, $table1.$columnName1 - $fixed, '0')");
    }

    public static function dbAddValueWithGraterThan($tableName1, $columnName1, $fixed, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            return DB::raw("IF($table1.$columnName1 + $fixed > 0, $table1.$columnName1 + $fixed, '0') AS $rename");
        }
        return DB::raw("IF($table1.$columnName1 + $fixed > 0, $table1.$columnName1 + $fixed, '0')");
    }

    public static function dbEqualZeroWithColumnAndFixed($tableName1, $columnName1, $tableName2, $columnName2, $fixed, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IF(IFNULL($table1.$columnName1, 0) = 0, $table2.$columnName2, '$fixed') AS $rename");
        }
        return DB::raw("IF(IFNULL($table1.$columnName1, 0) = 0, $table2.$columnName2, '$fixed')");
    }

    public static function dbEqualOneWithColumnAndFixed($tableName1, $columnName1, $tableName2, $columnName2, $fixed, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("IF(IFNULL($table1.$columnName1, 0) = 1, $table2.$columnName2, '$fixed') AS $rename");
        }
        return DB::raw("IF(IFNULL($table1.$columnName1, 0) = 1, $table2.$columnName2, '$fixed')");
    }

    public static function dbDateDiff($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("datediff($table1.$columnName1, $table2.$columnName2) AS $rename");
        }
        return DB::raw("datediff($table1.$columnName1, $table2.$columnName2)");
    }

    public static function dbSumMultiTwoFields($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("SUM($table1.$columnName1 * $table2.$columnName2) AS $rename");
        }
        return DB::raw("SUM($table1.$columnName1 * $table2.$columnName2)");
    }

    public static function dbTimeDiffToHours($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("TIME_TO_SEC(TIMEDIFF($table2.$columnName2, $table1.$columnName1))/3600 AS $rename");
        }
        return DB::raw("TIME_TO_SEC(TIMEDIFF($table2.$columnName2, $table1.$columnName1))/3600");
    }

    public static function dbTimeDiffToMins($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("TIME_TO_SEC(TIMEDIFF($table2.$columnName2, $table1.$columnName1))/60 AS $rename");
        }
        return DB::raw("TIME_TO_SEC(TIMEDIFF($table2.$columnName2, $table1.$columnName1))/60");
    }

    public static function dbTimeStampDiffToMins($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("TIMESTAMPDIFF(MINUTE, $table1.$columnName1, $table2.$columnName2) AS $rename");
        }
        return DB::raw("TIMESTAMPDIFF(MINUTE,$table1.$columnName1, $table2.$columnName2)");
    }

    public static function dbStockWeightField($tableName1, $columnName1, $tableName2, $columnName2, $tableName3, $columnName3, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        $table3 = DB::getTablePrefix() . $tableName3;
        if (!empty($rename)) {
            return DB::raw("SUM(ROUND(IF($table1.$columnName1 > 0 ,$table1.$columnName1, $table2.$columnName2 * $table3.$columnName3),2)) $rename");
        }
        return DB::raw("SUM(ROUND(IF($table1.$columnName1 > 0 ,$table1.$columnName1, $table2.$columnName2 * $table3.$columnName3),2))");
    }

    public static function dbGroupConcatWithOrder($tableName1, $columnName1, $columnName2, $sort, $seperator, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            return DB::raw("GROUP_CONCAT(DISTINCT $table1.$columnName1 ORDER BY $table1.$columnName2 $sort SEPARATOR '$seperator') $rename");
        }
        return DB::raw("GROUP_CONCAT(DISTINCT $table1.$columnName1 ORDER BY $table1.$columnName2 $sort SEPARATOR '$seperator')");
    }

    public static function dbMultiTwoFields($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("($table1.$columnName1 * $table2.$columnName2) AS $rename");
        }
        return DB::raw("($table1.$columnName1 * $table2.$columnName2)");
    }

    public static function dbDevideTwoFields($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("($table1.$columnName1 / $table2.$columnName2) AS $rename");
        }
        return DB::raw("($table1.$columnName1 * $table2.$columnName2)");
    }

    public static function dbSubTwoFields($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("($table1.$columnName1 - $table2.$columnName2) AS $rename");
        }
        return DB::raw("($table1.$columnName1 - $table2.$columnName2)");
    }

    public static function dbAbsFieldsWithTwoTables($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("($table1.$columnName1 - $table2.$columnName2) AS $rename");
        }
        return DB::raw("($table1.$columnName1 - $table2.$columnName2)");
    }

    public static function dbDateFormat($tableName1, $columnName1, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            return DB::raw("DATE_FORMAT($table1.$columnName1, '%d-%m-%Y') AS $rename");
        }
        return DB::raw("DATE_FORMAT($table1.$columnName1, '%d-%m-%Y')");
    }

    public static function dbDateFormatWithFormat($tableName1, $columnName1, $format, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        if (!empty($rename)) {
            return DB::raw("DATE_FORMAT($table1.$columnName1, '$format') AS $rename");
        }
        return DB::raw("DATE_FORMAT($table1.$columnName1, '$format')");
    }

    public static function dbInArray($tableName1, $columnName1, $array, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $arr = implode(",", $array);
        if (!empty($rename)) {
            return DB::raw("$table1.$columnName1 IN ($arr) AS $rename");
        }
        return DB::raw("$table1.$columnName1 IN ($arr)");
    }

    public static function dbMaxWithIfNull($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("MAX(IFNULL($table.$columnName, '$defaultVal')) AS $rename");
        }
        return DB::raw("MAX(IFNULL($table.$columnName, '$defaultVal'))");
    }

    public static function dbLeast($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("LEAST($table1.$columnName1, $table2.$columnName2) AS $rename");
        }
        return DB::raw("LEAST($table1.$columnName1, $table2.$columnName2)");
    }

    public static function dbGreatest($tableName1, $columnName1, $tableName2, $columnName2, $rename = "")
    {
        $table1 = DB::getTablePrefix() . $tableName1;
        $table2 = DB::getTablePrefix() . $tableName2;
        if (!empty($rename)) {
            return DB::raw("GREATEST($table1.$columnName1, $table2.$columnName2) AS $rename");
        }
        return DB::raw("GREATEST($table1.$columnName1, $table2.$columnName2)");
    }

    public static function dbDateAdd($tableName, $columnName, $defaultVal = "", $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("DATE_ADD($table.$columnName, INTERVAL '$defaultVal' DAY)) AS $rename");
        }
        return DB::raw("DATE_ADD($table.$columnName, INTERVAL '$defaultVal' DAY)");
    }

    public static function dbMonth($tableName, $columnName, $rename = "")
    {
        $table = DB::getTablePrefix() . $tableName;
        if (!empty($rename)) {
            return DB::raw("MONTH($table.$columnName) AS $rename");
        }
        return DB::raw("MONTH($table.$columnName)");
    }
}
