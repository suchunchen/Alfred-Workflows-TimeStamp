<?php
$timezone = 'Asia/Shanghai';
ini_set('date.timezone', $timezone);

require_once('workflows.php');

class TimeStamp {
    private function isDateTime($dateTime) {
        $ret = strtotime($dateTime);
        return $ret !== false && $ret != -1;
    }

    public function getTimeStamp($query) {
        $workflows = new Workflows();

        list($msec, $now) = explode(' ', microtime());
        $query = trim($query);
        $query = eval("return $query;");

        if ($query == 'now' || $query == "") {
            $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($now)) * 1000);
            $date = date('Y-m-d H:i:s',$now);

            $workflows->result(
                $query,
                $now,
                '当前时间戳【秒级】：'.$now,
                '',
                'icon.png',
                false,
            );

            $workflows->result(
                $query,
                $msectime,
                '当前时间戳【毫秒】：'.$msectime,
                '',
                'icon.png',
                false,
            );

            $workflows->result(
                $query,
                $date,
                '当前时间：'.$date,
                '',
                'icon.png',
                false,
            );

        } else if (is_numeric($query)) {
            // 毫秒时间戳转换
            switch (strlen($query)) {
                case 13:
                    $query = $query / 1000;
                    break;
                case 10:
                    break;
                default:
                    $query = $now + $query;
            }

            $cle = $query-$now;
            if ($cle > 0) {
                $d = floor($cle/3600/24);
                $h = floor(($cle%(3600*24))/3600);
                $m = floor(($cle%(3600*24))%3600/60);
                $s = floor(($cle%(3600*24))%60);
            } else if ($cle < 0) {
                $d = ceil($cle/3600/24);
                $h = ceil(($cle%(3600*24))/3600);
                $m = ceil(($cle%(3600*24))%3600/60);
                $s = ceil(($cle%(3600*24))%60);
            } else {
                $d = 0;
                $h = 0;
                $m = 0;
                $s = 0;
            }
            $workflows->result($query,
                date('Y-m-d H:i:s',$query),
                '目标时间：'.date('Y-m-d H:i:s',$query),
                "当前时间差 $d 天 $h 小时 $m 分 $s 秒",
                'icon.png',false);

        } else if ($this->isDateTime($query)) {
            $workflows->result($query,
                strtotime($query),
                '目标时间戳：'.strtotime($query),
                '与当前时间戳差：'.(strtotime($query)-$now).'秒',
                'icon.png',false);
        }

        echo $workflows->toxml();
    }
}