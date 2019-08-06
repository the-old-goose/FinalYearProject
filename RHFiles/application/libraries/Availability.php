<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class is used to create the calendar in the view
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Availability
{
    /**
     * @var string The proposed dates in a proposal
     */
    var $proposed_dates;

    public function __construct($proposed_dates=NULL)
    {
        $this->proposed_dates = $proposed_dates;
    }

    /**
     * Calculates the dates of the calendar
     * @return array $calendar The array containing values used to display the calendar
     */
    public function generate_calendar()
    {
        $calendar=array(
            'today'=>$this->date_today(),
            'max-days'=>22,
            'month-days'=>$this->get_month_days(),
            'proposed_dates'=>$this->get_proposed_dates());
            
        return $calendar;
    }

    /**
     * Formats a proposed or accepted date into its full string representation e.g Jan12 -> January 12th 2019
     * 
     * @param string  $date The date in the format JanXX or JanX
     * @return string $date The formatted date
     */
    public function get_date_string($date)
    {
        $month=substr($date,0,3);
        $day=substr($date,3, strlen($date)); 

        switch($month)
        {
            case "Jan":
                return 'January '.$this->ordinal($day)." ".date('Y');
                break;
            case "Feb":
                return 'Febuary '.$this->ordinal($day)." ".date('Y');
                break;
            case "Mar":
                return 'March '.$this->ordinal($day)." ".date('Y');
                break;
            case "Apr":
                return 'April '.$this->ordinal($day)." ".date('Y');
                break;
            case "May":
                return 'May '.$this->ordinal($day)." ".date('Y');
                break;
            case "Jun":
                return 'June '.$this->ordinal($day)." ".date('Y');
                break;
            case "Jul":
                return 'July '.$this->ordinal($day)." ".date('Y');
                break;
            case "Aug":
                return 'August '.$this->ordinal($day)." ".date('Y');
                break;
            case "Sep":
                return 'September '.$this->ordinal($day)." ".date('Y');
                break;
            case "Oct":
                return 'October '.$this->ordinal($day)." ".date('Y');
                break;
            case "Nov":
                return 'November '.$this->ordinal($day)." ".date('Y');
                break;
            case "Dec":
                return 'December '.$this->ordinal($day)." ".date('Y');
                break;
            default: return $date;
                
            }
        }
        
    /**
    * Adds the ordinal to a number
    *
    * @author Iacopo
    * @link <https://stackoverflow.com/questions/3109978/display-numbers-with-ordinal-suffix-in-php>
    * @param int  $number The number to have the ordinal appended to
    * @return string $number The number with the ordinal added i.e. 1 -> 1st, 10-> 10th
    */
    public function ordinal($number) 
    {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
           return $number. $ends[$number % 10];
    }

    /**
     * This function converts the proposed dates string into an array using ',' as a delimiter
     * 
     * @return array $dates The array of dates
     */
    private function parse_available_dates()
    {
         return $dates=explode(',',$this->proposed_dates[0]);
    }
         
    /**
     * This function builds a date based on a number
     * @param int $date A number in the format: 15
     * @return string $date The date in the format: Feb15
     */
    public function build_date($date)
    {
        if($date<date('j')) //date selected less than day of month it is
        {
            $date=date('M',strtotime('+1 month')).$date; //append a month prefix e.g Jan onto the date
        }
        else //else append the current months prefix e.g Dec
        {
            $date=date('M').$date;
        }

        return $date;
    }

    /**
     * This function returns todays date in the form of the day of the month
     * 
     * @return string $date The numerical representation of the date today e.g 15
     */
    public function date_today()
    {
        return date('j');
    }

    /**
     * This function returns number of days in the current month
     * 
     * @return string $date The numerical representation of the number of days in a month 28-31
     */
    public function get_month_days()
    {
        return date('t');
    }
        
    /**
     * This function returns converts the proposed date string into an associative array and returns it
     * 
     * @return array $proposed_dates The array representation of proposed dates
     */
    public function get_proposed_dates()
    {
        $proposed_dates=array();
        $dates=$this->parse_available_dates();
            
        foreach ($dates as $date)
        {
            $proposed_dates[$date]=$date;
        }
            
        return $proposed_dates;
    }
}
?>
