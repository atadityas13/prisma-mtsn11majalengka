<?php
$page_title = 'Dashboard Admin';
include_once __DIR__ . '/layout/header.php';

$db = new Database();

// Fetch summary stats
$db->query("SELECT COUNT(*) as total FROM guru");
$total_guru = $db->single()['total'];

$db->query("SELECT COUNT(*) as total FROM siswa");
$total_siswa = $db->single()['total'];

$db->query("SELECT COUNT(*) as total FROM mapel");
$total_mapel = $db->single()['total'];

$db->query("SELECT COUNT(*) as total FROM ploting_penguji");
$total_ploting = $db->single()['total'];

// Assessment progress (Sample data for chart)
$db->query("SELECT COUNT(*) as total FROM nilai_praktik");
$total_nilai = $db->single()['total'];

// Logic for progress percentage (Assume 1 score per aspek per student assignment)
// This is a simplified calculation for the dashboard
$progress_percent = $total_siswa > 0 ? round(($total_nilai / ($total_siswa * 5)) * 100, 1) : 0; 
if ($progress_percent > 100) $progress_percent = 100;
?>

<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat Datang, <?= $_SESSION['nama_lengkap'] ?>! 🎉</h5>
                        <p class="mb-4">
                            Progres penilaian ujian praktik saat ini telah mencapai <span class="fw-bold"><?= $progress_percent ?>%</span>. 
                            Pantau terus status penginputan nilai dari para guru penguji.
                        </p>
                        <a href="laporan.php" class="btn btn-sm btn-outline-primary">Lihat Laporan</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-start">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-2">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-primary p-2"><i class="bx bx-user-voice bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Guru</span>
                        <h3 class="card-title mb-2"><?= $total_guru ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-center mb-2">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-success p-2"><i class="bx bx-group bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Siswa</span>
                        <h3 class="card-title mb-2"><?= $total_siswa ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Statistics Card -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                    <h5 class="m-0 me-2">Statistik Master</h5>
                    <small class="text-muted">Ringkasan Data Aplikasi</small>
                </div>
            </div>
            <div class="card-body mt-4">
                <ul class="p-0 m-0">
                    <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="badge bg-label-info p-2"><i class="bx bx-book bx-sm"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Mata Pelajaran</h6>
                                <small class="text-muted">Total Mapel Praktik</small>
                            </div>
                            <div class="user-progress">
                                <small class="fw-semibold"><?= $total_mapel ?></small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="badge bg-label-warning p-2"><i class="bx bx-git-repo-forked bx-sm"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Ploting Penguji</h6>
                                <small class="text-muted">Tugas Guru Penguji</small>
                            </div>
                            <div class="user-progress">
                                <small class="fw-semibold"><?= $total_ploting ?></small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Statistics Card -->

    <!-- Assessment Progress Chart -->
    <div class="col-md-6 col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title">Progres Penilaian</h5>
            </div>
            <div class="card-body">
                <div id="assessmentProgressChart"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [<?= $progress_percent ?>],
            chart: {
                height: 350,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: '70%',
                    },
                    dataLabels: {
                        name: {
                            show: true,
                            fontSize: '16px',
                            fontWeight: 600,
                            offsetY: -10
                        },
                        value: {
                            show: true,
                            fontSize: '22px',
                            fontWeight: 700,
                            offsetY: 10,
                            formatter: function (val) {
                                return val + '%'
                            }
                        }
                    }
                }
            },
            labels: ['Total Progres'],
            colors: ['#696cff'],
        };

        var chart = new ApexCharts(document.querySelector("#assessmentProgressChart"), options);
        chart.render();
    });
</script>

<?php include_once __DIR__ . '/layout/footer.php'; ?>
