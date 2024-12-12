<?php
include("connect.php");

$aircraftFilter = $_GET['aircraftType'] ?? '';
$creditCardFilter = $_GET['creditCardType'] ?? '';
$sort = $_GET['sort'] ?? '';
$order = $_GET['order'] ?? 'ASC';

$flightsQuery = "SELECT * FROM flightlogs WHERE 1=1";
$types = '';  
$params = [];

if (!empty($aircraftFilter)) {
    $flightsQuery .= " AND aircraftType = ?";
    $types .= "s";  
    $params[] = $aircraftFilter;
}
if (!empty($creditCardFilter)) {
    $flightsQuery .= " AND creditCardType = ?";
    $types .= "s";  
    $params[] = $creditCardFilter;
}

$validSortColumns = ['pilotName', 'airlineName', 'flightNumber', 'ticketPrice', 'flightDurationMinutes'];
if (in_array($sort, $validSortColumns)) {
    $flightsQuery .= " ORDER BY $sort " . ($order === 'DESC' ? "DESC" : "ASC");
}

$stmt = mysqli_prepare($conn, $flightsQuery);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$flightResults = mysqli_stmt_get_result($stmt);

$aircraftQuery = "SELECT DISTINCT(aircraftType) FROM flightlogs";
$aircraftResults = mysqli_query($conn, $aircraftQuery);
if (!$aircraftResults) {
    die("Error fetching aircraft types: " . mysqli_error($conn));
}

$creditCardQuery = "SELECT DISTINCT(creditCardType) FROM flightlogs";
$creditCardResults = mysqli_query($conn, $creditCardQuery);
if (!$creditCardResults) {
    die("Error fetching credit card types: " . mysqli_error($conn));
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets/rowletticn.png">
    <title>Flight Logs Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<style>
  body {
    background: linear-gradient(to right,rgb(26, 73, 155),rgb(108, 167, 187),rgb(108, 167, 187),rgb(26, 73, 155)); 
    font-family: 'Pacifico', cursive;
  }
  
  .container h1{
    color: #ffffff;
    font-family: 'Pacifico', cursive;
    font-weight: bold;
    font-size: 2.5rem;
  }
</style>

<body>
<div class="container">
    <div class="text-center my-4">
        <h1>Airport Flight Logs - Admin Panel</h1>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="aircraftTypeSelect" class="form-label">Aircraft Type</label>
                <select id="aircraftTypeSelect" name="aircraftType" class="form-select">
                    <option value="">Any</option>
                    <?php while ($row = mysqli_fetch_assoc($aircraftResults)) { ?>
                        <option value="<?php echo $row['aircraftType']; ?>" <?php echo ($aircraftFilter === $row['aircraftType']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['aircraftType']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-4">
                <label for="creditCardTypeSelect" class="form-label">Credit Card Type</label>
                <select id="creditCardTypeSelect" name="creditCardType" class="form-select">
                    <option value="">Any</option>
                    <?php while ($row = mysqli_fetch_assoc($creditCardResults)) { ?>
                        <option value="<?php echo $row['creditCardType']; ?>" <?php echo ($creditCardFilter === $row['creditCardType']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['creditCardType']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="sort" class="form-label">Sort By</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="">None</option>
                    <?php foreach ($validSortColumns as $column) { ?>
                        <option value="<?php echo $column; ?>" <?php echo ($sort === $column) ? 'selected' : ''; ?>>
                            <?php echo ucwords(str_replace('flightDurationMinutes', 'Flight Duration', $column)); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="order" class="form-label">Order</label>
                <select id="order" name="order" class="form-select">
                    <option value="ASC" <?php echo ($order === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo ($order === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="container-fluid">
        <div class="row my-5">
            <div class="col">
            <div class="card p-4 rounded-5" style="border: 5px solid #2056e8;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Flight Number</th>
                                <th>Pilot Name</th>
                                <th>Aircraft Type</th>
                                <th>Airline Name</th>
                                <th>Flight Duration</th>
                                <th>Ticket Price</th>
                                <th>Credit Card Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($flightResults) > 0) {
                                while ($row = mysqli_fetch_assoc($flightResults)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['flightNumber']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pilotName']); ?></td>
                                        <td><?php echo htmlspecialchars($row['aircraftType']); ?></td>
                                        <td><?php echo htmlspecialchars($row['airlineName']); ?></td>
                                        <td>
                                            <?php
                                                $hours = intdiv($row['flightDurationMinutes'], 60);
                                                $minutes = $row['flightDurationMinutes'] % 60;
                                                echo "$hours hr $minutes min";
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['ticketPrice']); ?></td>
                                        <td><?php echo htmlspecialchars($row['creditCardType']); ?></td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center">No results found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
