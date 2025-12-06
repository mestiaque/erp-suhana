
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

.browser-used-box table thead th{
        color: #fff !important;
}

.value-tag {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: bold;
}

/* Colors */
.low-performance {
    background: #ea3a3b;  /* Red */
    color: #fff;
}
.medium-performance {
    background: #c8ffcd; /* Light Green */
    color: #000;
}
.high-performance {
    background: #00994d; /* Deep Green */
    color: #fff;
}


/* production report css */

   .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .header-info div {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
            background: #f8f9fa;
        }

        .metric-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .metric-label {
            color: #7f8c8d;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .metric-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #2c3e50;
        }

        .metric-subtext {
            color: #95a5a6;
            font-size: 0.85em;
            margin-top: 8px;
        }

        .efficiency {
            color: #27ae60;
        }

        .defect-rate {
            color: #e74c3c;
        }


        .production-table h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 1px;
        }

        tbody tr {
            border-bottom: 1px solid #ecf0f1;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        .status-good {
            color: #27ae60;
            font-weight: bold;
        }

        .status-warning {
            color: #f39c12;
            font-weight: bold;
        }

        .status-poor {
            color: #e74c3c;
            font-weight: bold;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        .summary {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary div {
            flex: 1;
            min-width: 150px;
        }

        .summary strong {
            display: block;
            font-size: 1.8em;
            margin-top: 5px;
        }
        .stats-card-box h3 {
            font-size: 22px;
            font-weight: 600;
        }






.header-info {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .data-table {
            background: white;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            font-size: 0.85rem;
        }
        .table .deliRport th {
            background-color: #6c757d;
            color: white;
            font-weight: 600;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
        }
        .table tbody td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .line-label {
            font-weight: 600;
            background-color: #e9ecef;
            text-align: left;
        }
        .data-row {
            font-family: monospace;
            font-size: 0.75rem;
        }
        .total-column {
            background-color: #effff6c7;
            font-weight: 600;
        }
        .date-header {
            font-size: 0.75rem;
            padding: 5px;
        }
        .crossed-out {
            text-decoration: line-through;
            color: #6c757d;
        }
        .notes-section {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }


        .line-label img {
            width: 35px;
            display: block;
            background: #19ff5b;
        }

        @media (max-width: 1400px) {
            .production-table h2 {
                font-size: 18px;
                font-weight: 600;
            }
            .breadcrumb-area h1 {
                font-size: 19px;
                font-weight: 500;
            }
            .card .card-header h3 {
                font-size: 16px;
                font-weight: 500;
            }
            .label {
                font-weight: 600;
                font-size: 12px;
            }
            h4 {
                font-size: 18px;
                font-weight: 500;
            }
            .stats-card-box h3 {
                font-size: 14px;
                font-weight: 600;
            }
            .stats-card-box {
                margin-bottom: 25px;
                padding: 15px 10px 10px 70px;
            }
            .stats-card-box .icon-box {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            .stats-card-box .sub-title {
                font-size: 14px;
            }


        }

        @media (max-width: 1240px) {
            .stats-card-box .sub-title {
                font-size: 12px;
            }
                .stats-card-box h3 {
                font-size: 12px;
            }
            .stats-card-box h3 .badge {
                font-size: 9px;
                        }
        }


        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }

            .metrics {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.85em;
            }

            th, td {
                padding: 10px 5px;
            }
        }



</style>
@endpush
