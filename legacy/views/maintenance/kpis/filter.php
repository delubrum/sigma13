<style>
    .loader-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div id="loaderOverlay" class="loader-overlay">
    <div class="spinner"></div>
    <p class="mt-2 text-blue-600 font-semibold">Loading data...</p>
</div>

<form id="monthYearForm" method="GET" action="index.php" class="flex justify-center">
    <input type="hidden" name="c" value="<?= $_REQUEST['c'] ?>">
    <input type="hidden" name="a" value="Kpis">
    <input type="hidden" name="in" value="<?= $_REQUEST['in'] ?? 1 ?>">

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 gap-6 items-center">
        <div class="flex flex-col items-center">
            <label for="selectYear" class="font-semibold text-gray-700 mb-2">Year:</label>
            <select name="year" id="selectYear" class="form-select form-select-sm p-2 border rounded-md">
                <?php
                    $currentYear = date('Y');
    $selectedYear = $_REQUEST['year'] ?? $currentYear;

    // FIXED RANGE: Current year and 5 years back
    for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
        $selected = ($y == $selectedYear) ? 'selected' : '';
        echo "<option value='$y' $selected>$y</option>";
    }
    ?>
            </select>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectYear = document.getElementById('selectYear');
        const loader = document.getElementById('loaderOverlay');
        const form = document.getElementById('monthYearForm');

        if (selectYear) {
            selectYear.addEventListener('change', function() {
                loader.style.display = 'flex';
                form.submit();
            });
        }
    });
</script>