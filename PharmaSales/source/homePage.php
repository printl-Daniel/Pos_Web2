<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
  header('Location: login.php'); 
  exit();
}
include '../server/graph.php';
$graph = new Graph();

$availableYears = $graph->getAvailableYears();
$currentYear = date('Y');
$year = isset($_GET['year']) ? $_GET['year'] : $currentYear; 

$revenueDataForYear = $graph->monthlyRevenue($year);
$salesVolumeDataForYear = $graph->monthlySalesVolume($year);
$monthlyProfitData = $graph->monthlyProfit($year);
$topOne = $graph->topOneProduct($year);
$topTwo = $graph->topTwoProduct($year);
$topThree = $graph->topThreeProduct($year);
$totalRevenue = $graph->totalYearlyRevenue($year);
$totalYearlyProfit = $graph->totalYearlyProfit($year);
$totalYearlySalesVolume = $graph->totalYearlySalesVolume($year);
$topOneVolume = $graph->topOneSaleVolumeYearly($year);
$topTwoVolume = $graph->topTwoSaleVolumeYearly($year);
$topThreeVolume = $graph->topThreeSaleVolumeYearly($year);
$currentRevenue = $graph->getCurrentMonthRevenue();
$predictedRevenue = $graph->predictNextThreeMonthsRevenue();

if (isset($_GET['ajax'])) {
  header('Content-Type: application/json');
  echo json_encode([
    'revenueData' => array_values($revenueDataForYear),
    'salesVolumeData' => array_values($salesVolumeDataForYear),
    'profitData' => array_values($monthlyProfitData),
    'topOne' => array_values($topOne),
    'topTwo' => array_values($topTwo),
    'topThree' => array_values($topThree),
    'totalYearlyRevenue' => $totalRevenue,
    'totalYearlyProfit' => $totalYearlyProfit,
    'totalYearlySalesVolume' => $totalYearlySalesVolume,
    'topOneSaleVolumeYearly' => $topOneVolume,
    'topTwoSaleVolumeYearly' => $topTwoVolume,
    'topThreeSaleVolumeYearly' => $topThreeVolume,
    'currentRevenue' => $currentRevenue,
    'predictedRevenue' => $predictedRevenue
  ]);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PharmaSales: Digital Inventory and Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/style/homePage.css" />
    <link rel="stylesheet" href="../assets/style/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="header">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container header-content">
                <div class="name">
                    <img src="../assets/images/Logo.png" class="img-fluid" width="200px" alt="Logo" />
                </div>
                <div class="collapse navbar-collapse mid-content" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item fs-6"><a href="#">Home</a></li>
                        <li class="nav-item fs-6"><a href="auditTrail.php">Audit Trail</a></li>
                        <li class="nav-item fs-6"><a href="inventory.php">Inventory</a></li>
                        <li class="nav-item fs-6"><a href="register.php">Register</a></li>
                        <li class="nav-item fs-6">
                            <a href="#" onclick="confirmLogout()" style="color: #39ccff">Logout</a>
                        </li>
                    </ul>
                </div>
                <div class="user">
                    <div class="user-info">
                        <small>name</small>
                    </div>
                    <img src="../assets/images/user.png" class="img-fluid" alt="user" width="40px" />
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>
    </div>
    <section class="home-section container-fluid">
        <div class="year-selector">
            <label for="yearSelect">PharmaSales Analytics for the Year</label>
            <select id="yearSelect" onchange="updateGraphYear()">
                <?php
        foreach ($availableYears as $yearOption) {
          echo "<option value='$yearOption'" . ($yearOption == $year ? " selected" : "") . ">$yearOption</option>";
        }
        ?>
            </select>
        </div>
        <div class="left-section">
            <div class="rev-volume">
                <div class="rev-vol">
                    <div class="rev-vol-text">
                        <div id="totalRevenue" class="rev">
                            <h5>Total Revenue for <?php echo $year; ?>:</h5>
                            <p>₱ <?php echo number_format($totalRevenue, 2); ?></p>
                        </div>
                        <div id="totalVolume">
                            <h5>Total Sales Volume for <?php echo $year; ?>:</h5>
                            <p><?php echo number_format($totalYearlySalesVolume); ?></p>
                        </div>
                    </div>
                    <div class="canv">
                        <canvas id="revVolChart"></canvas>
                    </div>
                </div>
                <div class="volume">
                    <div class="volume-text">
                        <div id="oneVolume">
                            <h5>Total Sales Volume of Top 1 Product in <?php echo $year; ?>:</h5>
                            <p><?php echo number_format($topOneVolume); ?></p>
                        </div>
                        <div id="twoVolume">
                            <h5>Total Sales Volume of Top 2 Product in <?php echo $year; ?>:</h5>
                            <p><?php echo number_format($topTwoVolume); ?></p>
                        </div>
                        <div id="threeVolume">
                            <h5>Total Sales Volume of Top 3 Product in <?php echo $year; ?>:</h5>
                            <p><?php echo number_format($topThreeVolume); ?></p>
                        </div>
                    </div>
                    <div class="canv">
                        <canvas id="combinedChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="bottom">
                <div class="prof-res">
                    <div class="profit-text">
                        <div id="totalProfit">
                            <h5>Total Profit for <?php echo $year; ?>:</h5>
                            <p>₱ <?php echo number_format($totalYearlyProfit, 2); ?></p>
                        </div>
                    </div>
                    <div class="canv">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
                <div class="pred-rev">
                    <h4>Predictive Analytics</h4>
                    <div class="pred-rev-text">
                        <div>
                            <h5>Current Revenue:</h5>
                            <p>₱ <?php echo number_format($currentRevenue, 2); ?></p>
                        </div>
                        <div>
                            <h5>Predicted Revenue After 3 Months:</h5>
                            <p>₱ <?php echo number_format($predictedRevenue, 2); ?></p>
                        </div>
                    </div>
                    <div class="canvPie">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../server/logout.php';
            }
        });
    }

    function updateGraphYear() {
        var selectedYear = document.getElementById('yearSelect').value;
        fetch(`homePage.php?ajax=true&year=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                if (data.topOne && data.topTwo && data.topThree) {
                    var salesTopOne = Array(12).fill(0);
                    var salesTopTwo = Array(12).fill(0);
                    var salesTopThree = Array(12).fill(0);

                    var topOneProductData = data.topOne;
                    var productNameTopOne = topOneProductData.length > 0 ? topOneProductData[0].name :
                        'Top Product 1';

                    var topTwoProductData = data.topTwo;
                    var productNameTopTwo = topTwoProductData.length > 0 ? topTwoProductData[0].name :
                        'Top Product 2';

                    var topThreeProductData = data.topThree;
                    var productNameTopThree = topThreeProductData.length > 0 ? topThreeProductData[0].name :
                        'Top Product 3';

                    topOneProductData.forEach(data => {
                        salesTopOne[data.saleMonth - 1] = data.totalSold;
                    });
                    topTwoProductData.forEach(data => {
                        salesTopTwo[data.saleMonth - 1] = data.totalSold;
                    });
                    topThreeProductData.forEach(data => {
                        salesTopThree[data.saleMonth - 1] = data.totalSold;
                    });

                    var months = ["January", "February", "March", "April", "May", "June", "July", "August",
                        "September",
                        "October", "November", "December"
                    ];

                    combinedChart.data.labels = months;
                    combinedChart.data.datasets[0].data = salesTopOne;
                    combinedChart.data.datasets[0].label = productNameTopOne;
                    combinedChart.data.datasets[1].data = salesTopTwo;
                    combinedChart.data.datasets[1].label = productNameTopTwo;
                    combinedChart.data.datasets[2].data = salesTopThree;
                    combinedChart.data.datasets[2].label = productNameTopThree;
                    combinedChart.options.plugins.title.text = `Monthly Sales Comparison for ${selectedYear}`;
                    combinedChart.update();
                }

                if (data.revenueData && data.salesVolumeData) {
                    revVol.data.datasets[0].data = data.revenueData;
                    revVol.data.datasets[1].data = data.salesVolumeData;
                    revVol.update();
                }

                if (data.profitData) {
                    profitChart.data.datasets[0].data = data.profitData;
                    profitChart.update();
                }
                document.getElementById('totalRevenue').innerHTML =
                    `<h5>Total Revenue for ${selectedYear}:</h5><p>₱${data.totalYearlyRevenue.toLocaleString(undefined, { minimumFractionDigits: 2 })}</p>`;
                document.getElementById('totalVolume').innerHTML =
                    `<h5>Total Sales Volume for ${selectedYear}:</h5><p>${data.totalYearlySalesVolume.toLocaleString()}</p>`;
                document.getElementById('oneVolume').innerHTML =
                    `<h5>Total Sales Volume of Top 1 Product in ${selectedYear}:</h5><p>${data.topOneSaleVolumeYearly.toLocaleString()}</p>`;
                document.getElementById('twoVolume').innerHTML =
                    `<h5>Total Sales Volume of Top 2 Product in ${selectedYear}:</h5><p>${data.topTwoSaleVolumeYearly.toLocaleString()}</p>`;
                document.getElementById('threeVolume').innerHTML =
                    `<h5>Total Sales Volume of Top 3 Product in ${selectedYear}:</h5><p>${data.topThreeSaleVolumeYearly.toLocaleString()}</p>`;
                document.getElementById('totalProfit').innerHTML =
                    `<h5>Total Profit for ${selectedYear}:</h5><p>₱${data.totalYearlyProfit.toLocaleString(undefined, { minimumFractionDigits: 2 })}</p>`;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                response.text().then(text => console.log(text));
            });
    }



    var data =
        <?php echo json_encode(['currentRevenue' => $currentRevenue, 'predictedRevenue' => $predictedRevenue]); ?>;
    const ctx3 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: ['Current Revenue', 'Predicted Revenue'],
            datasets: [{
                data: [data.currentRevenue, data.predictedRevenue],
                backgroundColor: [
                    'rgba(119, 230, 102, 0.2)',
                    'rgba(57, 204, 255, 0.2)'
                ],
                borderColor: [
                    'rgba(119, 230, 102, 1)',
                    'rgba(57, 204, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            }
        }
    });

    var topOneProductData = <?php echo json_encode(array_values($topOne)); ?>;
    var salesTopOne = Array(12).fill(0);
    var productNameTopOne = topOneProductData.length > 0 ? topOneProductData[0].name : 'Top Product 1';
    topOneProductData.forEach(data => {
        salesTopOne[data.saleMonth - 1] = data.totalSold;
    });

    var topTwoProductData = <?php echo json_encode(array_values($topTwo)); ?>;
    var salesTopTwo = Array(12).fill(0);
    var productNameTopTwo = topTwoProductData.length > 0 ? topTwoProductData[0].name : 'Top Product 2';
    topTwoProductData.forEach(data => {
        salesTopTwo[data.saleMonth - 1] = data.totalSold;
    });

    var topThreeProductData = <?php echo json_encode(array_values($topThree)); ?>;
    var salesTopThree = Array(12).fill(0);
    var productNameTopThree = topThreeProductData.length > 0 ? topThreeProductData[0].name : 'Top Product 3';
    topThreeProductData.forEach(data => {
        salesTopThree[data.saleMonth - 1] = data.totalSold;
    });



    var ctx2 = document.getElementById('combinedChart').getContext('2d');
    var combinedChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October",
                "November", "December"
            ],
            datasets: [{
                    label: productNameTopOne,
                    data: salesTopOne,
                    backgroundColor: 'rgba(119, 230, 102, 0.2)',
                    borderColor: 'rgba(119, 230, 102, 1)',
                    borderWidth: 1,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: productNameTopTwo,
                    data: salesTopTwo,
                    backgroundColor: 'rgba(57, 204, 255, 0.2)',
                    borderColor: 'rgba(57, 204, 255, 1)',
                    borderWidth: 1,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: productNameTopThree,
                    data: salesTopThree,
                    backgroundColor: 'rgba(138, 231, 242, 0.2)',
                    borderColor: 'rgba(138, 231, 242, 1)',
                    borderWidth: 1,
                    fill: true,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sold'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: `Top Three Products of ${selectedYear}`
                }
            }
        }
    });


    var selectedYear = document.getElementById('yearSelect').value;

    var ctx1 = document.getElementById('profitChart').getContext('2d');
    var profitChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October",
                "November", "December"
            ],
            datasets: [{
                label: 'Monthly Profit',
                data: <?php echo json_encode(array_values($monthlyProfitData)); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#77e666',
                pointBorderWidth: 2,
                pointRadius: 3,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var initialRevenueData = <?php echo json_encode(array_values($revenueDataForYear)); ?>;
    var initialSalesVolumeData = <?php echo json_encode(array_values($salesVolumeDataForYear)); ?>;
    var ctx = document.getElementById('revVolChart').getContext('2d');
    var revVol = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October",
                "November", "December"
            ],
            datasets: [{
                    label: 'Monthly Revenue',
                    data: initialRevenueData,
                    backgroundColor: 'rgba(138, 231, 242, 0.5)',
                    borderColor: '#8ae7f2',
                    borderWidth: 1,
                    yAxisID: 'y',
                },
                {
                    label: 'Monthly Sales Volume ',
                    data: initialSalesVolumeData,
                    backgroundColor: 'rgba(121, 231, 111, 0.5)',
                    borderColor: '#79e76f',
                    borderWidth: 1,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Sales Volume'
                    }
                }
            }
        }
    });
    </script>
</body>

</html>