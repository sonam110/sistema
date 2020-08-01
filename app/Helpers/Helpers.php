<?php
use App\booking;

function getLast30Days()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        $dateList[] = '"'.$date->format("Y-m-d").'"';
    }
    $allDates = implode(', ', $dateList);
    return $allDates;
}

function getLast30DaysSaleCounts()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        if(auth()->user()->hasRole('admin'))
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->count();
        }
        else
        {
            $sale[] = booking::whereDate('created_at', $date->format("Y-m-d"))->where('created_by', auth()->id())->count();
        }
    }
    $totalSale = implode(', ', $sale);
    return $totalSale;
}

function getLast30DaysSale()
{
    $today     = new \DateTime();
    $begin     = $today->sub(new \DateInterval('P30D'));
    $end       = new \DateTime();
    $end       = $end->modify('+1 day');
    $interval  = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        if(auth()->user()->hasRole('admin'))
        {
            $sales = booking::whereDate('created_at', $date->format("Y-m-d"))->get();
        }
        else
        {
            $sales = booking::whereDate('created_at', $date->format("Y-m-d"))->where('created_by', auth()->id())->get();
        }
    }
    return $sales;
}