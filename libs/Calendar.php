<?php
namespace Tools;

class Calendar
{
    private $year; //当前的年
    private $month; //当前的月
    private $start_weekday; //当月的第一天对应的是周几
    private $days; //当前月一共多少天
    public function __construct()
    {
        $this->year  = isset($_GET["year"]) ? $_GET["year"] : date("Y");
        $this->month = isset($_GET["month"]) ? $_GET["month"] : date("m");

        $this->start_weekday = date("w", mktime(0, 0, 0, $this->month, 1, $this->year));
        $this->days          = date("t", mktime(0, 0, 0, $this->month, 1, $this->year));
    }
    public function out()
    {
        echo '<table align="center">';
        $this->chageDate("test.php");
        $this->weeksList();
        $this->daysList();
        echo '</table>';
    }
    private function weeksList()
    {
        $week = array('日', '一', '二', '三', '四', '五', '六');
        echo '<tr>';
        for ($i = 0; $i < count($week); $i++) {
            echo '<th class="fontb">' . $week[$i] . '</th>';
        }

        echo '</tr>';
    }
    private function daysList()
    {
        echo '<tr>';
        //输出空格(当前一月第一天前面要空出来)
        for ($j = 0; $j < $this->start_weekday; $j++) {
            echo '<td> </td>';
        }

        for ($k = 1; $k <= $this->days; $k++) {
            $j++;
            if ($k == date('d')) {
                echo '<td class="fontb">' . $k . '</td>';
            } else {
                echo '<td>' . $k . '</td>';
            }

            if ($j % 7 == 0) {
                echo '</tr><tr>';
            }

        }
        //后面几个空格
        while ($j % 7 !== 0) {
            echo '<td> </td>';
            $j++;
        }
        echo '</tr>';
    }
    private function prevYear($year, $month)
    {
        $year = $year - 1;

        if ($year < 1970) {
            $year = 1970;
        }

        return "year={$year}&month={$month}";
    }

    private function prevMonth($year, $month)
    {
        if ($month == 1) {
            $year = $year - 1;

            if ($year < 1970) {
                $year = 1970;
            }

            $month = 12;
        } else {
            $month--;
        }
        return "year={$year}&month={$month}";
    }

    private function nextYear($year, $month)
    {
        $year = $year + 1;
        if ($year > 2038) {
            $year = 2038;
        }

        return "year={$year}&month={$month}";
    }

    private function nextMonth($year, $month)
    {
        if ($month == 12) {
            $year++;
            if ($year > 2100) {
                $year = 2100;
            }

            $month = 1;
        } else {
            $month++;
        }

        return "year={$year}&month={$month}";
    }
    private function chageDate($url = "")
    {
        echo '<tr>';
        echo '<td><a href="?' . $this->prevYear($this->year, $this->month) . '">' . '<<' . '</a></td>';
        echo '<td><a href="?' . $this->prevMonth($this->year, $this->month) . '">' . '<' . '</a></td>';
        echo '<td colspan="3">';
        echo '<form>';
        echo '<select name="year" onchange="window.location=\'' . $url . '?year=\'+this.options[selectedIndex].value+\'&month=' . $this->month . '\'">';
        for ($sy = 1970; $sy <= 2100; $sy++) {
            $selected = ($sy == $this->year) ? "selected" : "";
            echo '<option ' . $selected . ' value="' . $sy . '">' . $sy . '</option>';
        }
        echo '</select>';
        echo '<select name="month"  onchange="window.location=\'' . $url . '?year=' . $this->year . '&month=\'+this.options[selectedIndex].value">';
        for ($sm = 1; $sm <= 12; $sm++) {
            $selected1 = ($sm == $this->month) ? "selected" : "";
            echo '<option ' . $selected1 . ' value="' . $sm . '">' . $sm . '</option>';
        }
        echo '</select>';
        echo '</form>';
        echo '</td>';

        echo '<td><a href="?' . $this->nextYear($this->year, $this->month) . '">' . '>>' . '</a></td>';
        echo '<td><a href="?' . $this->nextMonth($this->year, $this->month) . '">' . '>' . '</a></td>';
        echo '</tr>';
    }
}
