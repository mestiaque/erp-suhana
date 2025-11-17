@extends('Admin.layouts.app')
@section('title')
<title>Admin Dashboard</title>
@endsection

@push('css')
<style>
 

.stats-card-box .icon-box {
    display: flex;
    align-items: center;
    justify-content: center
}

.stats-card-box .sub-title {
    color: #000;
}


 .animated-list{
    list-style:none;
    padding:0;
    margin:0;
    display: flex;
    margin-left: 10px;
  }

  .animated-list li{
    display:flex;
    align-items:center;
    gap:12px;
    margin-left: 10px;
    background:#fff;
    padding:10px 12px;
    border-radius:10px;
    margin-bottom:10px;
    box-shadow:0 6px 14px rgba(14,18,36,0.06);
    transform:translateY(10px);
    opacity:0;
    animation: slideIn .45s ease forwards;
  }

  /* stagger */
  .animated-list li:nth-child(1){ animation-delay:.08s; }
  .animated-list li:nth-child(2){ animation-delay:.18s; }
  .animated-list li:nth-child(3){ animation-delay:.28s; }

  .bullet{
    width:14px;
    height:14px;
    border-radius:50%;
    flex-shrink:0;
    position:relative;
    box-shadow:0 2px 6px rgba(0,0,0,0.12);
    display:inline-block;
  }

  .label{
    font-weight:600;
  }
  .sub{ font-size:13px; color:var(--muted); margin-left:4px; font-weight:500; }

  /* pulsing ring */
  .bullet::after{
    content:"";
    position:absolute;
    left:50%; top:50%;
    transform:translate(-50%,-50%);
    width:100%;
    height:100%;
    border-radius:50%;
    opacity:.25;
    animation: pulse 1.6s infinite ease-out;
  }

  /* colors */
  .b1{ background:green; }
  .b1::after{ background: green; }
  .b2{ background: red; }
  .b2::after{ background: red; }
  .b3{ background: blue; }
  .b3::after{ background: blue; }

  @keyframes pulse{
    0%{ transform:translate(-50%,-50%) scale(.9); opacity:.28; }
    70%{ transform:translate(-50%,-50%) scale(2.2); opacity:0; }
    100%{ opacity:0; }
  }

  @keyframes slideIn{
    to{ transform:none; opacity:1; }
  }


.card-header {
    display: flex;
    align-items: center;
}
h4{
    font-size: 20px;
}





</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <!-- Breadcrumb -->
    <div class="breadcrumb-area">
        <h1>Admin Dashboard</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
            <li class="item">Dashboard</li>
        </ol>
    </div>

    <h4 class="mb-3"><i class="fa fa-clipboard"></i> Order Summary</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-money"></i>
                </div>
                <span class="sub-title">Order total</span>
                <h3>120 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Confirm order</span>
                <h3>150 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-clock-o"></i>
                </div>
                <span class="sub-title">Peding order</span>
                <h3>333 <span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-times-circle"></i>
                </div>
                <span class="sub-title">Cansel order</span>
                <h3>100 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 26.0%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3"><i class="fa fa-users"></i> Office Staff</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #d81be9;">
                    <i class="fa fa-user-friends"></i>
                </div>
                <span class="sub-title">Total Staff</span>
                <h3>1000 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #185806;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <span class="sub-title">Present Staff</span>
                <h3>760 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #b20c0c;">
                    <i class="fa fa-times-circle"></i>
                </div>
                <span class="sub-title">Absent Staff</span>
                <h3>123<span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box" style="background-color: #ea043a;">
                    <i class="fa fa-briefcase"></i>
                </div>
                <span class="sub-title">Worker Present Staff</span>
                <h3>856 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3"><i class="fa fa-money"></i> Accounts</h4>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-line-chart"></i>
                </div>
                <span class="sub-title">Total Sale Amount</span>
                <h3>13456780 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 56.9%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="56.9" style="width: 56.9%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-file-text"></i>
                </div>
                <span class="sub-title">Order Amount</span>
                <h3>13456780 <span class="badge"><i class="bx bx-up-arrow-alt"></i> 32.1%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="32.1" style="width: 32.1%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-credit-card"></i>
                </div>
                <span class="sub-title">Total Expense Amount</span>
                <h3>{{priceFullFormat($reports['total_expenses'])}}<span class="badge badge-red"><i class="bx bx-down-arrow-alt"></i> 45.5%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="45.5" style="width: 45.5%;"></div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stats-card-box">
                <div class="icon-box">
                    <i class="fa fa-credit-card"></i>
                </div>
                <span class="sub-title">Net Profit / Balance</span>
                <h3>13456780<span class="badge"><i class="bx bx-up-arrow-alt"></i> 88.0%</span></h3>

                <div class="progress-list">
                    <div class="bar-inner">
                        <div class="bar progress-line" data-width="26.0" style="width: 26%;"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-30">
                        <div class="card-header">
                            <h3><i class="fa fa-line-chart"></i> Yearly Charts</h3>
                            <ul class="animated-list" aria-label="Status list">
                                <li>
                                    <span class="bullet b1" aria-hidden="true"></span>
                                    <div>
                                    <div class="label">80% Sent <span class="sub">• Sent</span></div>
                                    </div>
                                </li>

                                <li>
                                    <span class="bullet b2" aria-hidden="true"></span>
                                    <div>
                                    <div class="label">75% Read <span class="sub">• Read</span></div>
                                    </div>
                                </li>

                                <li>
                                    <span class="bullet b3" aria-hidden="true"></span>
                                    <div>
                                    <div class="label">33% Unread <span class="sub">• Unread</span></div>
                                    </div>
                                </li>
                                </ul>
                           
                        </div>

                        <div class="card-body" style="position: relative;">
                            <div id="website-analytics-chart" class="extra-margin" style="min-height: 320px;"><div id="apexchartsoo42krx2" class="apexcharts-canvas apexchartsoo42krx2 light" style="width: 888px; height: 305px;"><svg id="SvgjsSvg2001" width="888" height="305" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><foreignObject x="0" y="0" width="888" height="305"><div class="apexcharts-legend center position-bottom" xmlns="http://www.w3.org/1999/xhtml" style="inset: auto 0px 10px; position: absolute;"><div class="apexcharts-legend-series" rel="1" data:collapsed="false" style="margin: 0px 5px;"><span class="apexcharts-legend-marker" rel="1" data:collapsed="false" style="background: rgb(234, 58, 59); color: rgb(234, 58, 59); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span><span class="apexcharts-legend-text" rel="1" i="0" data:default-text="Net%20Profit" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-family: Helvetica, Arial, sans-serif;">Net Profit</span></div><div class="apexcharts-legend-series" rel="2" data:collapsed="false" style="margin: 0px 5px;"><span class="apexcharts-legend-marker" rel="2" data:collapsed="false" style="background: rgb(71, 136, 255); color: rgb(71, 136, 255); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span><span class="apexcharts-legend-text" rel="2" i="1" data:default-text="Revenue" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-family: Helvetica, Arial, sans-serif;">Revenue</span></div><div class="apexcharts-legend-series" rel="3" data:collapsed="false" style="margin: 0px 5px;"><span class="apexcharts-legend-marker" rel="3" data:collapsed="false" style="background: rgb(106, 79, 252); color: rgb(106, 79, 252); height: 12px; width: 12px; left: 0px; top: 0px; border-width: 0px; border-color: rgb(255, 255, 255); border-radius: 2px;"></span><span class="apexcharts-legend-text" rel="3" i="2" data:default-text="Free%20Cash%20Flow" data:collapsed="false" style="color: rgb(55, 61, 63); font-size: 12px; font-family: Helvetica, Arial, sans-serif;">Free Cash Flow</span></div></div><style type="text/css">	
    	
      .apexcharts-legend {	
        display: flex;	
        overflow: auto;	
        padding: 0 10px;	
      }	
      .apexcharts-legend.position-bottom, .apexcharts-legend.position-top {	
        flex-wrap: wrap	
      }	
      .apexcharts-legend.position-right, .apexcharts-legend.position-left {	
        flex-direction: column;	
        bottom: 0;	
      }	
      .apexcharts-legend.position-bottom.left, .apexcharts-legend.position-top.left, .apexcharts-legend.position-right, .apexcharts-legend.position-left {	
        justify-content: flex-start;	
      }	
      .apexcharts-legend.position-bottom.center, .apexcharts-legend.position-top.center {	
        justify-content: center;  	
      }	
      .apexcharts-legend.position-bottom.right, .apexcharts-legend.position-top.right {	
        justify-content: flex-end;	
      }	
      .apexcharts-legend-series {	
        cursor: pointer;	
        line-height: normal;	
      }	
      .apexcharts-legend.position-bottom .apexcharts-legend-series, .apexcharts-legend.position-top .apexcharts-legend-series{	
        display: flex;	
        align-items: center;	
      }	
      .apexcharts-legend-text {	
        position: relative;	
        font-size: 14px;	
      }	
      .apexcharts-legend-text *, .apexcharts-legend-marker * {	
        pointer-events: none;	
      }	
      .apexcharts-legend-marker {	
        position: relative;	
        display: inline-block;	
        cursor: pointer;	
        margin-right: 3px;	
      }	
      	
      .apexcharts-legend.right .apexcharts-legend-series, .apexcharts-legend.left .apexcharts-legend-series{	
        display: inline-block;	
      }	
      .apexcharts-legend-series.no-click {	
        cursor: auto;	
      }	
      .apexcharts-legend .apexcharts-hidden-zero-series, .apexcharts-legend .apexcharts-hidden-null-series {	
        display: none !important;	
      }	
      .inactive-legend {	
        opacity: 0.45;	
      }</style></foreignObject><g id="SvgjsG2003" class="apexcharts-inner apexcharts-graphical" transform="translate(55.359375, 40)"><defs id="SvgjsDefs2002"><linearGradient id="SvgjsLinearGradient2006" x1="0" y1="0" x2="0" y2="1"><stop id="SvgjsStop2007" stop-opacity="0.4" stop-color="rgba(216,227,240,0.4)" offset="0"></stop><stop id="SvgjsStop2008" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop><stop id="SvgjsStop2009" stop-opacity="0.5" stop-color="rgba(190,209,230,0.5)" offset="1"></stop></linearGradient><clipPath id="gridRectMaskoo42krx2"><rect id="SvgjsRect2011" width="824.640625" height="210.348" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath><clipPath id="gridRectMarkerMaskoo42krx2"><rect id="SvgjsRect2012" width="824.640625" height="210.348" x="-1" y="-1" rx="0" ry="0" fill="#ffffff" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0"></rect></clipPath></defs><rect id="SvgjsRect2010" width="12.464251893939393" height="208.348" x="103.71449464740175" y="0" rx="0" ry="0" fill="url(#SvgjsLinearGradient2006)" opacity="1" stroke-width="0" stroke-dasharray="3" class="apexcharts-xcrosshairs" y2="208.348" filter="none" fill-opacity="0.9" x1="103.71449464740175" x2="103.71449464740175"></rect><g id="SvgjsG2054" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG2055" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"><text id="SvgjsText2056" font-family="Helvetica, Arial, sans-serif" x="34.276692708333336" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2057" style="font-family: Helvetica, Arial, sans-serif;">Jan</tspan><title>Jan</title></text><text id="SvgjsText2058" font-family="Helvetica, Arial, sans-serif" x="102.830078125" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2059" style="font-family: Helvetica, Arial, sans-serif;">Feb</tspan><title>Feb</title></text><text id="SvgjsText2060" font-family="Helvetica, Arial, sans-serif" x="171.38346354166666" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2061" style="font-family: Helvetica, Arial, sans-serif;">Mar</tspan><title>Mar</title></text><text id="SvgjsText2062" font-family="Helvetica, Arial, sans-serif" x="239.93684895833334" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2063" style="font-family: Helvetica, Arial, sans-serif;">Apr</tspan><title>Apr</title></text><text id="SvgjsText2064" font-family="Helvetica, Arial, sans-serif" x="308.49023437500006" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2065" style="font-family: Helvetica, Arial, sans-serif;">May</tspan><title>May</title></text><text id="SvgjsText2066" font-family="Helvetica, Arial, sans-serif" x="377.04361979166674" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2067" style="font-family: Helvetica, Arial, sans-serif;">Jun</tspan><title>Jun</title></text><text id="SvgjsText2068" font-family="Helvetica, Arial, sans-serif" x="445.5970052083334" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2069" style="font-family: Helvetica, Arial, sans-serif;">Jul</tspan><title>Jul</title></text><text id="SvgjsText2070" font-family="Helvetica, Arial, sans-serif" x="514.150390625" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2071" style="font-family: Helvetica, Arial, sans-serif;">Aug</tspan><title>Aug</title></text><text id="SvgjsText2072" font-family="Helvetica, Arial, sans-serif" x="582.7037760416666" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2073" style="font-family: Helvetica, Arial, sans-serif;">Sep</tspan><title>Sep</title></text><text id="SvgjsText2074" font-family="Helvetica, Arial, sans-serif" x="651.2571614583333" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2075" style="font-family: Helvetica, Arial, sans-serif;">Oct</tspan><title>Oct</title></text><text id="SvgjsText2076" font-family="Helvetica, Arial, sans-serif" x="719.8105468749999" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2077" style="font-family: Helvetica, Arial, sans-serif;">Nov</tspan><title>Nov</title></text><text id="SvgjsText2078" font-family="Helvetica, Arial, sans-serif" x="788.3639322916665" y="237.348" text-anchor="middle" dominant-baseline="auto" font-size="12px" font-weight="400" fill="#373d3f" class="apexcharts-xaxis-label " style="font-family: Helvetica, Arial, sans-serif;"><tspan id="SvgjsTspan2079" style="font-family: Helvetica, Arial, sans-serif;">Dec</tspan><title>Dec</title></text></g><line id="SvgjsLine2080" x1="0" y1="209.348" x2="822.640625" y2="209.348" stroke="#78909c" stroke-dasharray="0" stroke-width="1"></line></g><g id="SvgjsG2088" class="apexcharts-grid"><g id="SvgjsG2089" class="apexcharts-gridlines-horizontal"><line id="SvgjsLine2102" x1="0" y1="0" x2="822.640625" y2="0" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line><line id="SvgjsLine2103" x1="0" y1="52.087" x2="822.640625" y2="52.087" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line><line id="SvgjsLine2104" x1="0" y1="104.174" x2="822.640625" y2="104.174" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line><line id="SvgjsLine2105" x1="0" y1="156.26100000000002" x2="822.640625" y2="156.26100000000002" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line><line id="SvgjsLine2106" x1="0" y1="208.348" x2="822.640625" y2="208.348" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line></g><g id="SvgjsG2090" class="apexcharts-gridlines-vertical"></g><line id="SvgjsLine2091" x1="68.55338541666667" y1="209.348" x2="68.55338541666667" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2092" x1="137.10677083333334" y1="209.348" x2="137.10677083333334" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2093" x1="205.66015625" y1="209.348" x2="205.66015625" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2094" x1="274.2135416666667" y1="209.348" x2="274.2135416666667" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2095" x1="342.76692708333337" y1="209.348" x2="342.76692708333337" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2096" x1="411.32031250000006" y1="209.348" x2="411.32031250000006" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2097" x1="479.87369791666674" y1="209.348" x2="479.87369791666674" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2098" x1="548.4270833333334" y1="209.348" x2="548.4270833333334" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2099" x1="616.98046875" y1="209.348" x2="616.98046875" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2100" x1="685.5338541666666" y1="209.348" x2="685.5338541666666" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2101" x1="754.0872395833333" y1="209.348" x2="754.0872395833333" y2="215.348" stroke="#78909c" stroke-dasharray="0" class="apexcharts-xaxis-tick"></line><line id="SvgjsLine2108" x1="0" y1="208.348" x2="822.640625" y2="208.348" stroke="transparent" stroke-dasharray="0"></line><line id="SvgjsLine2107" x1="0" y1="1" x2="0" y2="208.348" stroke="transparent" stroke-dasharray="0"></line></g><g id="SvgjsG2014" class="apexcharts-bar-series apexcharts-plot-series"><g id="SvgjsG2015" class="apexcharts-series" rel="1" seriesName="NetxProfit" data:realIndex="0"><path id="SvgjsPath2017" d="M 18.69637784090909 208.348L 18.69637784090909 131.95373333333333L 29.160629734848484 131.95373333333333L 29.160629734848484 208.348L 17.69637784090909 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 18.69637784090909 208.348L 18.69637784090909 131.95373333333333L 29.160629734848484 131.95373333333333L 29.160629734848484 208.348L 17.69637784090909 208.348" pathFrom="M 18.69637784090909 208.348L 18.69637784090909 208.348L 29.160629734848484 208.348L 29.160629734848484 208.348L 17.69637784090909 208.348" cy="131.95373333333333" cx="92.48188920454545" j="0" val="44" barHeight="76.39426666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2018" d="M 93.48188920454545 208.348L 93.48188920454545 112.85516666666668L 103.94614109848484 112.85516666666668L 103.94614109848484 208.348L 92.48188920454545 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 93.48188920454545 208.348L 93.48188920454545 112.85516666666668L 103.94614109848484 112.85516666666668L 103.94614109848484 208.348L 92.48188920454545 208.348" pathFrom="M 93.48188920454545 208.348L 93.48188920454545 208.348L 103.94614109848484 208.348L 103.94614109848484 208.348L 92.48188920454545 208.348" cy="112.85516666666668" cx="167.2674005681818" j="1" val="55" barHeight="95.49283333333334" barWidth="12.464251893939393"></path><path id="SvgjsPath2019" d="M 168.2674005681818 208.348L 168.2674005681818 109.3827L 178.73165246212122 109.3827L 178.73165246212122 208.348L 167.2674005681818 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 168.2674005681818 208.348L 168.2674005681818 109.3827L 178.73165246212122 109.3827L 178.73165246212122 208.348L 167.2674005681818 208.348" pathFrom="M 168.2674005681818 208.348L 168.2674005681818 208.348L 178.73165246212122 208.348L 178.73165246212122 208.348L 167.2674005681818 208.348" cy="109.3827" cx="242.0529119318182" j="2" val="57" barHeight="98.96530000000001" barWidth="12.464251893939393"></path><path id="SvgjsPath2020" d="M 243.0529119318182 208.348L 243.0529119318182 111.11893333333335L 253.5171638257576 111.11893333333335L 253.5171638257576 208.348L 242.0529119318182 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 243.0529119318182 208.348L 243.0529119318182 111.11893333333335L 253.5171638257576 111.11893333333335L 253.5171638257576 208.348L 242.0529119318182 208.348" pathFrom="M 243.0529119318182 208.348L 243.0529119318182 208.348L 253.5171638257576 208.348L 253.5171638257576 208.348L 242.0529119318182 208.348" cy="111.11893333333335" cx="316.83842329545456" j="3" val="56" barHeight="97.22906666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2021" d="M 317.83842329545456 208.348L 317.83842329545456 102.43776666666668L 328.30267518939394 102.43776666666668L 328.30267518939394 208.348L 316.83842329545456 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 317.83842329545456 208.348L 317.83842329545456 102.43776666666668L 328.30267518939394 102.43776666666668L 328.30267518939394 208.348L 316.83842329545456 208.348" pathFrom="M 317.83842329545456 208.348L 317.83842329545456 208.348L 328.30267518939394 208.348L 328.30267518939394 208.348L 316.83842329545456 208.348" cy="102.43776666666668" cx="391.62393465909093" j="4" val="61" barHeight="105.91023333333334" barWidth="12.464251893939393"></path><path id="SvgjsPath2022" d="M 392.62393465909093 208.348L 392.62393465909093 107.64646666666667L 403.0881865530303 107.64646666666667L 403.0881865530303 208.348L 391.62393465909093 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 392.62393465909093 208.348L 392.62393465909093 107.64646666666667L 403.0881865530303 107.64646666666667L 403.0881865530303 208.348L 391.62393465909093 208.348" pathFrom="M 392.62393465909093 208.348L 392.62393465909093 208.348L 403.0881865530303 208.348L 403.0881865530303 208.348L 391.62393465909093 208.348" cy="107.64646666666667" cx="466.4094460227273" j="5" val="58" barHeight="100.70153333333334" barWidth="12.464251893939393"></path><path id="SvgjsPath2023" d="M 467.4094460227273 208.348L 467.4094460227273 98.9653L 477.8736979166667 98.9653L 477.8736979166667 208.348L 466.4094460227273 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 467.4094460227273 208.348L 467.4094460227273 98.9653L 477.8736979166667 98.9653L 477.8736979166667 208.348L 466.4094460227273 208.348" pathFrom="M 467.4094460227273 208.348L 467.4094460227273 208.348L 477.8736979166667 208.348L 477.8736979166667 208.348L 466.4094460227273 208.348" cy="98.9653" cx="541.1949573863636" j="6" val="63" barHeight="109.38270000000001" barWidth="12.464251893939393"></path><path id="SvgjsPath2024" d="M 542.1949573863636 208.348L 542.1949573863636 104.174L 552.659209280303 104.174L 552.659209280303 208.348L 541.1949573863636 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 542.1949573863636 208.348L 542.1949573863636 104.174L 552.659209280303 104.174L 552.659209280303 208.348L 541.1949573863636 208.348" pathFrom="M 542.1949573863636 208.348L 542.1949573863636 208.348L 552.659209280303 208.348L 552.659209280303 208.348L 541.1949573863636 208.348" cy="104.174" cx="615.98046875" j="7" val="60" barHeight="104.174" barWidth="12.464251893939393"></path><path id="SvgjsPath2025" d="M 616.98046875 208.348L 616.98046875 93.7566L 627.4447206439394 93.7566L 627.4447206439394 208.348L 615.98046875 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 616.98046875 208.348L 616.98046875 93.7566L 627.4447206439394 93.7566L 627.4447206439394 208.348L 615.98046875 208.348" pathFrom="M 616.98046875 208.348L 616.98046875 208.348L 627.4447206439394 208.348L 627.4447206439394 208.348L 615.98046875 208.348" cy="93.7566" cx="690.7659801136364" j="8" val="66" barHeight="114.59140000000001" barWidth="12.464251893939393"></path><path id="SvgjsPath2026" d="M 691.7659801136364 208.348L 691.7659801136364 15.626100000000008L 702.2302320075758 15.626100000000008L 702.2302320075758 208.348L 690.7659801136364 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 691.7659801136364 208.348L 691.7659801136364 15.626100000000008L 702.2302320075758 15.626100000000008L 702.2302320075758 208.348L 690.7659801136364 208.348" pathFrom="M 691.7659801136364 208.348L 691.7659801136364 208.348L 702.2302320075758 208.348L 702.2302320075758 208.348L 690.7659801136364 208.348" cy="15.626100000000008" cx="765.5514914772727" j="9" val="111" barHeight="192.7219" barWidth="12.464251893939393"></path><path id="SvgjsPath2027" d="M 766.5514914772727 208.348L 766.5514914772727 60.76816666666667L 777.0157433712121 60.76816666666667L 777.0157433712121 208.348L 765.5514914772727 208.348" fill="rgba(234,58,59,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="0" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 766.5514914772727 208.348L 766.5514914772727 60.76816666666667L 777.0157433712121 60.76816666666667L 777.0157433712121 208.348L 765.5514914772727 208.348" pathFrom="M 766.5514914772727 208.348L 766.5514914772727 208.348L 777.0157433712121 208.348L 777.0157433712121 208.348L 765.5514914772727 208.348" cy="60.76816666666667" cx="840.3370028409091" j="10" val="85" barHeight="147.57983333333334" barWidth="12.464251893939393"></path><g id="SvgjsG2016" class="apexcharts-datalabels"></g></g><g id="SvgjsG2028" class="apexcharts-series" rel="2" seriesName="Revenue" data:realIndex="1"><path id="SvgjsPath2030" d="M 31.160629734848484 208.348L 31.160629734848484 76.39426666666668L 41.624881628787875 76.39426666666668L 41.624881628787875 208.348L 30.160629734848484 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 31.160629734848484 208.348L 31.160629734848484 76.39426666666668L 41.624881628787875 76.39426666666668L 41.624881628787875 208.348L 30.160629734848484 208.348" pathFrom="M 31.160629734848484 208.348L 31.160629734848484 208.348L 41.624881628787875 208.348L 41.624881628787875 208.348L 30.160629734848484 208.348" cy="76.39426666666668" cx="104.94614109848484" j="0" val="76" barHeight="131.95373333333333" barWidth="12.464251893939393"></path><path id="SvgjsPath2031" d="M 105.94614109848484 208.348L 105.94614109848484 60.76816666666667L 116.41039299242424 60.76816666666667L 116.41039299242424 208.348L 104.94614109848484 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 105.94614109848484 208.348L 105.94614109848484 60.76816666666667L 116.41039299242424 60.76816666666667L 116.41039299242424 208.348L 104.94614109848484 208.348" pathFrom="M 105.94614109848484 208.348L 105.94614109848484 208.348L 116.41039299242424 208.348L 116.41039299242424 208.348L 104.94614109848484 208.348" cy="60.76816666666667" cx="179.73165246212122" j="1" val="85" barHeight="147.57983333333334" barWidth="12.464251893939393"></path><path id="SvgjsPath2032" d="M 180.73165246212122 208.348L 180.73165246212122 32.98843333333335L 191.19590435606062 32.98843333333335L 191.19590435606062 208.348L 179.73165246212122 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 180.73165246212122 208.348L 180.73165246212122 32.98843333333335L 191.19590435606062 32.98843333333335L 191.19590435606062 208.348L 179.73165246212122 208.348" pathFrom="M 180.73165246212122 208.348L 180.73165246212122 208.348L 191.19590435606062 208.348L 191.19590435606062 208.348L 179.73165246212122 208.348" cy="32.98843333333335" cx="254.5171638257576" j="2" val="101" barHeight="175.35956666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2033" d="M 255.5171638257576 208.348L 255.5171638257576 38.19713333333334L 265.981415719697 38.19713333333334L 265.981415719697 208.348L 254.5171638257576 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 255.5171638257576 208.348L 255.5171638257576 38.19713333333334L 265.981415719697 38.19713333333334L 265.981415719697 208.348L 254.5171638257576 208.348" pathFrom="M 255.5171638257576 208.348L 255.5171638257576 208.348L 265.981415719697 208.348L 265.981415719697 208.348L 254.5171638257576 208.348" cy="38.19713333333334" cx="329.30267518939394" j="3" val="98" barHeight="170.15086666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2034" d="M 330.30267518939394 208.348L 330.30267518939394 57.29570000000001L 340.7669270833333 57.29570000000001L 340.7669270833333 208.348L 329.30267518939394 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 330.30267518939394 208.348L 330.30267518939394 57.29570000000001L 340.7669270833333 57.29570000000001L 340.7669270833333 208.348L 329.30267518939394 208.348" pathFrom="M 330.30267518939394 208.348L 330.30267518939394 208.348L 340.7669270833333 208.348L 340.7669270833333 208.348L 329.30267518939394 208.348" cy="57.29570000000001" cx="404.0881865530303" j="4" val="87" barHeight="151.0523" barWidth="12.464251893939393"></path><path id="SvgjsPath2035" d="M 405.0881865530303 208.348L 405.0881865530303 26.043499999999995L 415.5524384469697 26.043499999999995L 415.5524384469697 208.348L 404.0881865530303 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 405.0881865530303 208.348L 405.0881865530303 26.043499999999995L 415.5524384469697 26.043499999999995L 415.5524384469697 208.348L 404.0881865530303 208.348" pathFrom="M 405.0881865530303 208.348L 405.0881865530303 208.348L 415.5524384469697 208.348L 415.5524384469697 208.348L 404.0881865530303 208.348" cy="26.043499999999995" cx="478.8736979166667" j="5" val="105" barHeight="182.30450000000002" barWidth="12.464251893939393"></path><path id="SvgjsPath2036" d="M 479.8736979166667 208.348L 479.8736979166667 50.35076666666666L 490.33794981060606 50.35076666666666L 490.33794981060606 208.348L 478.8736979166667 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 479.8736979166667 208.348L 479.8736979166667 50.35076666666666L 490.33794981060606 50.35076666666666L 490.33794981060606 208.348L 478.8736979166667 208.348" pathFrom="M 479.8736979166667 208.348L 479.8736979166667 208.348L 490.33794981060606 208.348L 490.33794981060606 208.348L 478.8736979166667 208.348" cy="50.35076666666666" cx="553.659209280303" j="6" val="91" barHeight="157.99723333333336" barWidth="12.464251893939393"></path><path id="SvgjsPath2037" d="M 554.659209280303 208.348L 554.659209280303 10.417399999999986L 565.1234611742424 10.417399999999986L 565.1234611742424 208.348L 553.659209280303 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 554.659209280303 208.348L 554.659209280303 10.417399999999986L 565.1234611742424 10.417399999999986L 565.1234611742424 208.348L 553.659209280303 208.348" pathFrom="M 554.659209280303 208.348L 554.659209280303 208.348L 565.1234611742424 208.348L 565.1234611742424 208.348L 553.659209280303 208.348" cy="10.417399999999986" cx="628.4447206439394" j="7" val="114" barHeight="197.93060000000003" barWidth="12.464251893939393"></path><path id="SvgjsPath2038" d="M 629.4447206439394 208.348L 629.4447206439394 43.405833333333334L 639.9089725378788 43.405833333333334L 639.9089725378788 208.348L 628.4447206439394 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 629.4447206439394 208.348L 629.4447206439394 43.405833333333334L 639.9089725378788 43.405833333333334L 639.9089725378788 208.348L 628.4447206439394 208.348" pathFrom="M 629.4447206439394 208.348L 629.4447206439394 208.348L 639.9089725378788 208.348L 639.9089725378788 208.348L 628.4447206439394 208.348" cy="43.405833333333334" cx="703.2302320075758" j="8" val="95" barHeight="164.94216666666668" barWidth="12.464251893939393"></path><path id="SvgjsPath2039" d="M 704.2302320075758 208.348L 704.2302320075758 43.405833333333334L 714.6944839015151 43.405833333333334L 714.6944839015151 208.348L 703.2302320075758 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 704.2302320075758 208.348L 704.2302320075758 43.405833333333334L 714.6944839015151 43.405833333333334L 714.6944839015151 208.348L 703.2302320075758 208.348" pathFrom="M 704.2302320075758 208.348L 704.2302320075758 208.348L 714.6944839015151 208.348L 714.6944839015151 208.348L 703.2302320075758 208.348" cy="43.405833333333334" cx="778.0157433712121" j="9" val="95" barHeight="164.94216666666668" barWidth="12.464251893939393"></path><path id="SvgjsPath2040" d="M 779.0157433712121 208.348L 779.0157433712121 69.44933333333333L 789.4799952651515 69.44933333333333L 789.4799952651515 208.348L 778.0157433712121 208.348" fill="rgba(71,136,255,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="1" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 779.0157433712121 208.348L 779.0157433712121 69.44933333333333L 789.4799952651515 69.44933333333333L 789.4799952651515 208.348L 778.0157433712121 208.348" pathFrom="M 779.0157433712121 208.348L 779.0157433712121 208.348L 789.4799952651515 208.348L 789.4799952651515 208.348L 778.0157433712121 208.348" cy="69.44933333333333" cx="852.8012547348485" j="10" val="80" barHeight="138.89866666666668" barWidth="12.464251893939393"></path><g id="SvgjsG2029" class="apexcharts-datalabels"></g></g><g id="SvgjsG2041" class="apexcharts-series" rel="3" seriesName="FreexCashxFlow" data:realIndex="2"><path id="SvgjsPath2043" d="M 43.624881628787875 208.348L 43.624881628787875 147.57983333333334L 54.089133522727266 147.57983333333334L 54.089133522727266 208.348L 42.624881628787875 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 43.624881628787875 208.348L 43.624881628787875 147.57983333333334L 54.089133522727266 147.57983333333334L 54.089133522727266 208.348L 42.624881628787875 208.348" pathFrom="M 43.624881628787875 208.348L 43.624881628787875 208.348L 54.089133522727266 208.348L 54.089133522727266 208.348L 42.624881628787875 208.348" cy="147.57983333333334" cx="117.41039299242424" j="0" val="35" barHeight="60.76816666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2044" d="M 118.41039299242424 208.348L 118.41039299242424 137.16243333333335L 128.87464488636363 137.16243333333335L 128.87464488636363 208.348L 117.41039299242424 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 118.41039299242424 208.348L 118.41039299242424 137.16243333333335L 128.87464488636363 137.16243333333335L 128.87464488636363 208.348L 117.41039299242424 208.348" pathFrom="M 118.41039299242424 208.348L 118.41039299242424 208.348L 128.87464488636363 208.348L 128.87464488636363 208.348L 117.41039299242424 208.348" cy="137.16243333333335" cx="192.1959043560606" j="1" val="41" barHeight="71.18556666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2045" d="M 193.1959043560606 208.348L 193.1959043560606 145.8436L 203.66015625 145.8436L 203.66015625 208.348L 192.1959043560606 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 193.1959043560606 208.348L 193.1959043560606 145.8436L 203.66015625 145.8436L 203.66015625 208.348L 192.1959043560606 208.348" pathFrom="M 193.1959043560606 208.348L 193.1959043560606 208.348L 203.66015625 208.348L 203.66015625 208.348L 192.1959043560606 208.348" cy="145.8436" cx="266.981415719697" j="2" val="36" barHeight="62.504400000000004" barWidth="12.464251893939393"></path><path id="SvgjsPath2046" d="M 267.981415719697 208.348L 267.981415719697 163.20593333333335L 278.4456676136364 163.20593333333335L 278.4456676136364 208.348L 266.981415719697 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 267.981415719697 208.348L 267.981415719697 163.20593333333335L 278.4456676136364 163.20593333333335L 278.4456676136364 208.348L 266.981415719697 208.348" pathFrom="M 267.981415719697 208.348L 267.981415719697 208.348L 278.4456676136364 208.348L 278.4456676136364 208.348L 266.981415719697 208.348" cy="163.20593333333335" cx="341.76692708333337" j="3" val="26" barHeight="45.14206666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2047" d="M 342.76692708333337 208.348L 342.76692708333337 130.2175L 353.23117897727275 130.2175L 353.23117897727275 208.348L 341.76692708333337 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 342.76692708333337 208.348L 342.76692708333337 130.2175L 353.23117897727275 130.2175L 353.23117897727275 208.348L 341.76692708333337 208.348" pathFrom="M 342.76692708333337 208.348L 342.76692708333337 208.348L 353.23117897727275 208.348L 353.23117897727275 208.348L 341.76692708333337 208.348" cy="130.2175" cx="416.55243844696975" j="4" val="45" barHeight="78.13050000000001" barWidth="12.464251893939393"></path><path id="SvgjsPath2048" d="M 417.55243844696975 208.348L 417.55243844696975 125.00880000000001L 428.0166903409091 125.00880000000001L 428.0166903409091 208.348L 416.55243844696975 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 417.55243844696975 208.348L 417.55243844696975 125.00880000000001L 428.0166903409091 125.00880000000001L 428.0166903409091 208.348L 416.55243844696975 208.348" pathFrom="M 417.55243844696975 208.348L 417.55243844696975 208.348L 428.0166903409091 208.348L 428.0166903409091 208.348L 416.55243844696975 208.348" cy="125.00880000000001" cx="491.3379498106061" j="5" val="48" barHeight="83.3392" barWidth="12.464251893939393"></path><path id="SvgjsPath2049" d="M 492.3379498106061 208.348L 492.3379498106061 118.06386666666667L 502.8022017045455 118.06386666666667L 502.8022017045455 208.348L 491.3379498106061 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 492.3379498106061 208.348L 492.3379498106061 118.06386666666667L 502.8022017045455 118.06386666666667L 502.8022017045455 208.348L 491.3379498106061 208.348" pathFrom="M 492.3379498106061 208.348L 492.3379498106061 208.348L 502.8022017045455 208.348L 502.8022017045455 208.348L 491.3379498106061 208.348" cy="118.06386666666667" cx="566.1234611742424" j="6" val="52" barHeight="90.28413333333334" barWidth="12.464251893939393"></path><path id="SvgjsPath2050" d="M 567.1234611742424 208.348L 567.1234611742424 116.32763333333334L 577.5877130681818 116.32763333333334L 577.5877130681818 208.348L 566.1234611742424 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 567.1234611742424 208.348L 567.1234611742424 116.32763333333334L 577.5877130681818 116.32763333333334L 577.5877130681818 208.348L 566.1234611742424 208.348" pathFrom="M 567.1234611742424 208.348L 567.1234611742424 208.348L 577.5877130681818 208.348L 577.5877130681818 208.348L 566.1234611742424 208.348" cy="116.32763333333334" cx="640.9089725378788" j="7" val="53" barHeight="92.02036666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2051" d="M 641.9089725378788 208.348L 641.9089725378788 137.16243333333335L 652.3732244318181 137.16243333333335L 652.3732244318181 208.348L 640.9089725378788 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 641.9089725378788 208.348L 641.9089725378788 137.16243333333335L 652.3732244318181 137.16243333333335L 652.3732244318181 208.348L 640.9089725378788 208.348" pathFrom="M 641.9089725378788 208.348L 641.9089725378788 208.348L 652.3732244318181 208.348L 652.3732244318181 208.348L 640.9089725378788 208.348" cy="137.16243333333335" cx="715.6944839015151" j="8" val="41" barHeight="71.18556666666667" barWidth="12.464251893939393"></path><path id="SvgjsPath2052" d="M 716.6944839015151 208.348L 716.6944839015151 52.08699999999999L 727.1587357954545 52.08699999999999L 727.1587357954545 208.348L 715.6944839015151 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 716.6944839015151 208.348L 716.6944839015151 52.08699999999999L 727.1587357954545 52.08699999999999L 727.1587357954545 208.348L 715.6944839015151 208.348" pathFrom="M 716.6944839015151 208.348L 716.6944839015151 208.348L 727.1587357954545 208.348L 727.1587357954545 208.348L 715.6944839015151 208.348" cy="52.08699999999999" cx="790.4799952651515" j="9" val="90" barHeight="156.26100000000002" barWidth="12.464251893939393"></path><path id="SvgjsPath2053" d="M 791.4799952651515 208.348L 791.4799952651515 34.72466666666668L 801.9442471590909 34.72466666666668L 801.9442471590909 208.348L 790.4799952651515 208.348" fill="rgba(106,79,252,1)" fill-opacity="1" stroke="transparent" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-bar-area" index="2" clip-path="url(#gridRectMaskoo42krx2)" pathTo="M 791.4799952651515 208.348L 791.4799952651515 34.72466666666668L 801.9442471590909 34.72466666666668L 801.9442471590909 208.348L 790.4799952651515 208.348" pathFrom="M 791.4799952651515 208.348L 791.4799952651515 208.348L 801.9442471590909 208.348L 801.9442471590909 208.348L 790.4799952651515 208.348" cy="34.72466666666668" cx="865.2655066287879" j="10" val="100" barHeight="173.62333333333333" barWidth="12.464251893939393"></path><g id="SvgjsG2042" class="apexcharts-datalabels"></g></g></g><line id="SvgjsLine2109" x1="0" y1="0" x2="822.640625" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine2110" x1="0" y1="0" x2="822.640625" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG2111" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG2112" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG2113" class="apexcharts-point-annotations"></g></g><g id="SvgjsG2081" class="apexcharts-yaxis" rel="0" transform="translate(22.359375, 0)"><g id="SvgjsG2082" class="apexcharts-yaxis-texts-g"><text id="SvgjsText2083" font-family="Helvetica, Arial, sans-serif" x="20" y="41.4" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="regular" fill="#373d3f" class="apexcharts-yaxis-label " style="font-family: Helvetica, Arial, sans-serif;">120</text><text id="SvgjsText2084" font-family="Helvetica, Arial, sans-serif" x="20" y="93.58700000000002" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="regular" fill="#373d3f" class="apexcharts-yaxis-label " style="font-family: Helvetica, Arial, sans-serif;">90</text><text id="SvgjsText2085" font-family="Helvetica, Arial, sans-serif" x="20" y="145.77400000000003" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="regular" fill="#373d3f" class="apexcharts-yaxis-label " style="font-family: Helvetica, Arial, sans-serif;">60</text><text id="SvgjsText2086" font-family="Helvetica, Arial, sans-serif" x="20" y="197.96100000000004" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="regular" fill="#373d3f" class="apexcharts-yaxis-label " style="font-family: Helvetica, Arial, sans-serif;">30</text><text id="SvgjsText2087" font-family="Helvetica, Arial, sans-serif" x="20" y="250.14800000000005" text-anchor="end" dominant-baseline="auto" font-size="11px" font-weight="regular" fill="#373d3f" class="apexcharts-yaxis-label " style="font-family: Helvetica, Arial, sans-serif;">0</text></g></g></svg><div class="apexcharts-tooltip light" style="left: 165.306px; top: 65px;"><div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;">Feb</div><div class="apexcharts-tooltip-series-group active" style="display: flex;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(71, 136, 255);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label">Revenue: </span><span class="apexcharts-tooltip-text-value">$ 85 thousands</span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="display: none;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(71, 136, 255);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label">Revenue: </span><span class="apexcharts-tooltip-text-value">$ 85 thousands</span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div><div class="apexcharts-tooltip-series-group" style="display: none;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(71, 136, 255);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-label">Revenue: </span><span class="apexcharts-tooltip-text-value">$ 85 thousands</span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-toolbar"><div class="apexcharts-menu-icon" title="Menu"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"></path><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg></div><div class="apexcharts-menu"><div class="apexcharts-menu-item exportSVG" title="Download SVG">Download SVG</div><div class="apexcharts-menu-item exportPNG" title="Download PNG">Download PNG</div></div></div></div></div>
                        <div class="resize-triggers"><div class="expand-trigger"><div style=""></div></div><div class="contract-trigger"></div></div></div>
                    </div>      
        </div>
        <div class="col-lg-4">
                <div class="card mb-30">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3><i class="fa-regular fa-user"></i> Login Users</h3>

                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-show"></i> View
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-edit-alt"></i> Edit
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-trash"></i> Delete
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-printer"></i> Print
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <i class="bx bx-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body browser-used-box">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>MD. Nasim Billah</td>
                                        <td>01745354374</td>
                                    </tr>
                                    <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                     <tr>
                                        <td>MD. Nasim Billah</td>
                                        <td>01745354374</td>
                                    </tr>
                                    <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                      <tr>
                                        <td>Emon Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                    <tr>
                                        <td>MD. Rabiul Hasan</td>
                                        <td>01475354374</td>
                                    </tr>
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>
@endsection

@push('js')
<!-- Custom JS for admin -->
@endpush