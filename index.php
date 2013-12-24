<?
////////////////////////////////////////////////////////////////////////////////
// C O N F I G U R A T I O N
$logEntriesPerPage = 10;

////////////////////////////////////////////////////////////////////////////////
// D A T A B A S E   A C C E S S
$dbServer = "mysql21.secureserver.net";
$dbUsername = "heddings";
$dbPassword = "jIcTIY3Ed1gI";
$dbDatabase = "heddings";
$dbTable_Flights = "logbook";

////////////////////////////////////////////////////////////////////////////////
// P A R A M E T E R S
$start = (isset($_GET["start"]) ? $_GET["start"] : 0);
$sort = (isset($_GET["sort"]) ? $_GET["sort"] : "date");
$direc = (isset($_GET["direc"]) ? $_GET["direc"] : "ASC");

//printf("(%d/%s/%s)<br>\n", $start, $sort, $direc);

////////////////////////////////////////////////////////////////////////////////
// D B   C O N N E C T I O N
$dbConn = mysql_connect($dbServer, $dbUsername, $dbPassword) or die("<font color=red>DB CONNECTION ERROR</font>");
mysql_select_db($dbDatabase);

// load all the current flights
$page_flights = array();
$sql  = "SELECT * FROM logbook ORDER BY " . $sort . " " . $direc;
$sql .= " LIMIT " . $start . "," . $logEntriesPerPage . ";";
$result = mysql_query($sql);
//printf("%s\n%s\n", $sql, mysql_error());
while ($row_data = mysql_fetch_assoc($result)) {
	$page_flights[] = $row_data;
}

// use this array for the totals
$all_flights = array();
$result = mysql_query("SELECT * FROM logbook;");
while ($row_data = mysql_fetch_assoc($result)) {
	$all_flights[] = $row_data;
}
$n_flights = count($all_flights);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="/styles.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Jason Heddings' Flight Log</title>
</head>

<body bgcolor="#AABBAA">
	<div class="text">
		Jump To Page:
		<? if ($start > 0) { ?>
			<a class="page-list" href="/<?= ($start - $logEntriesPerPage) ?>/<?= $sort ?>/<?= $direc ?>">&lt; PREV</a> |
		<? } ?>

		<? for ($page = 0; ($page * $logEntriesPerPage) < $n_flights; $page++) { ?>
			<a class="page-list" href="/<?= ($page * $logEntriesPerPage) ?>/<?= $sort ?>/<?= $direc ?>"><?= $page + 1 ?></a>
			<? if ((($page + 1) * $logEntriesPerPage) < $n_flights) { ?> | <? } ?>
		<? } ?>

		<? if (($start + $logEntriesPerPage) < $n_flights) { ?>
			| <a class="page-list" href="/<?= ($start + $logEntriesPerPage) ?>/<?= $sort ?>/<?= $direc ?>">NEXT &gt;</a>
		<? } ?>
	</div>

	<table border="1" cellpadding="3" cellspacing="1" bgcolor="#EEEEEE">
		<tr bgcolor="#334455">
			<!--td rowspan="2" align="center" class="table-header">#</td-->
			<td rowspan="2" align="center" class="table-header">
				<a class="table-header" href="/<?= $start ?>/date/<?= ($direc == "ASC" ? "DESC" : "ASC") ?>">
					Date
					<? if ($sort == "date") { ?>
						<img src="/images/<?= strtolower($direc) ?>.png" border="0" align="absmiddle">
					<? } ?>
				</a>
			</td>
			<td rowspan="2" align="center" class="table-header">Aircraft Type</td>
			<td rowspan="2" align="center" class="table-header">Aircraft Ident</td>
			<td colspan="2" align="center" nowrap class="table-header">FLIGHT ROUTE </td>
			<td rowspan="2" align="center" class="table-header">Remarks &amp; Endorsements</td>
			<td rowspan="2" align="center" class="table-header"># T/O</td>
			<td rowspan="2" align="center" class="table-header"># LDG</td>
			<td colspan="4" align="center" nowrap class="table-header">AIRCRAFT CATEGORY </td>
			<td colspan="6" align="center" class="table-header">CONDITIONS OF FLIGHT </td>
			<td colspan="2" rowspan="2" align="center" class="table-header">Flight Simulator</td>
			<td colspan="8" align="center" class="table-header">TYPE OF FLIGHT </td>
			<td colspan="2" rowspan="2" align="center" class="table-header">Total Duration of Flight</td>
		</tr>
		<tr bgcolor="#334455">
		  <td align="center" class="table-header">FROM</td>
		  <td align="center" class="table-header">TO</td>
		  <td class="table-header" align="center" colspan="2">Single Engine Land</td>
	    <td align="center" class="table-header" colspan="2">Multi Engine Land</td>
		  <td align="center" class="table-header" colspan="2" width="50">Night</td>
		  <td align="center" class="table-header" colspan="2">Instrument - Actual</td>
		  <td colspan="2" align="center" class="table-header">Instrument - Simulated</td>
		  <td colspan="2" align="center" class="table-header">Cross Country</td>
		  <td colspan="2" align="center" class="table-header">As Flight Instructor</td>
		  <td colspan="2" align="center" class="table-header">Dual Received</td>
		  <td colspan="2" align="center" class="table-header">Pilot In Command</td>
		</tr>

		<? foreach ($page_flights as $flight) { ?>
			<tr>
				<td class="table-text">
                  <? if (isset($flight["ref_url"]) && ($flight["ref_url"] != "")) { ?>
                    <a href="<?= $flight["ref_url"] ?>"><?= $flight["date"] ?></a>
                  <? } else { ?>
                    <?= $flight["date"] ?>
                  <? } ?>
                </td>
				<td class="table-text"><?= $flight["aircraft_type"] ?></td>
				<td class="table-text"><?= ident_link($flight["aircraft_ident"]) ?></td>
				<td class="table-text"><?= airport_link($flight["rte_from"]) ?></td>
				<td class="table-text"><?= airport_link($flight["rte_to"]) ?></td>
				<td class="remarks"><?= $flight["remarks"] ?></td>
				<td class="table-text" align="right" bgcolor="#CCCCCC"><?= $flight["nr_to"] ?></td>
				<td class="table-text" align="right" bgcolor="#CCCCCC"><?= $flight["nr_ldg"] ?></td>
				<?= split_hrs($flight["hrs_sgl_eng_land"], "table-text") ?>
				<?= split_hrs($flight["hrs_multi_eng_land"], "table-text", "#CCCCCC") ?>
				<?= split_hrs($flight["hrs_night"], "table-text") ?>
				<?= split_hrs($flight["hrs_instr"], "table-text", "#CCCCCC") ?>
				<?= split_hrs($flight["hrs_hood"], "table-text") ?>
				<?= split_hrs($flight["hrs_simulator"], "table-text", "#CCCCCC") ?>
				<?= split_hrs($flight["hrs_xcountry"], "table-text") ?>
				<?= split_hrs($flight["hrs_instructor"], "table-text", "#CCCCCC") ?>
				<?= split_hrs($flight["hrs_dual"], "table-text") ?>
				<?= split_hrs($flight["hrs_pic"], "table-text", "#CCCCCC") ?>
				<?= split_hrs($flight["hrs_total"], "table-text") ?>
			</tr>
		<? } ?>

		<tr bgcolor="#334455">
		  <td colspan="6" align="center" class="table-header">&nbsp;</td>
		  <td align="center" class="table-header"># T/O</td>
		  <td align="center" class="table-header"># LDG</td>
		  <td class="table-header" align="center" colspan="2">Single Engine Land</td>
	    <td align="center" class="table-header" colspan="2">Multi Engine Land</td>
		  <td align="center" class="table-header" colspan="2" width="50">Night</td>
		  <td align="center" class="table-header" colspan="2">Instrument - Actual</td>
		  <td colspan="2" align="center" class="table-header">Instrument - Simulated</td>
		  <td colspan="2" align="center" class="table-header">Flight Simulator</td>
		  <td colspan="2" align="center" class="table-header">Cross Country</td>
		  <td colspan="2" align="center" class="table-header">As Flight Instructor</td>
		  <td colspan="2" align="center" class="table-header">Dual Received</td>
		  <td colspan="2" align="center" class="table-header">Pilot In Command</td>
		  <td colspan="2" align="center" class="table-header">Total Duration of Flight</td>
		</tr>
		<tr bgcolor="#CCDDCC">
			<td colspan="6" class="table-totals" align="right">PAGE TOTALS:</td>
			<td class="table-totals" align="right"><?= page_total("nr_to") ?></td>
			<td class="table-totals" align="right"><?= page_total("nr_ldg") ?></td>
			<?= split_hrs(page_total("hrs_sgl_eng_land"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_multi_eng_land"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_night"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_instr"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_hood"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_simulator"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_xcountry"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_instructor"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_dual"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_pic"), "table-totals") ?>
			<?= split_hrs(page_total("hrs_total"), "table-totals") ?>
		</tr>
		<tr bgcolor="#CCDDCC">
			<td colspan="6" class="table-totals" align="right">TOTALS TO DATE:</td>
			<td class="table-totals" align="right"><?= flights_total("nr_to") ?></td>
			<td class="table-totals" align="right"><?= flights_total("nr_ldg") ?></td>
			<?= split_hrs(flights_total("hrs_sgl_eng_land"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_multi_eng_land"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_night"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_instr"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_hood"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_simulator"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_xcountry"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_instructor"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_dual"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_pic"), "table-totals") ?>
			<?= split_hrs(flights_total("hrs_total"), "table-totals") ?>
		</tr>
	</table>
</body>
</html>

<?
////////////////////////////////////////////////////////////////////////////////
// F U N C T I O N S

////////////////////////////////////////////////////////////////////////////////

function ident_link($ident) {
	return "<a href=\"http://162.58.35.241/acdatabase/NNumSQL.asp?NNumbertxt=" . substr($ident, 1) . "\">" . $ident . "</a>";
}

////////////////////////////////////////////////////////////////////////////////

function airport_link($ident) {
	return ((strlen($ident) != 3) ? $ident : "<a href=\"http://www.airnav.com/airport/" . $ident . "\">" . $ident . "</a>");
}

////////////////////////////////////////////////////////////////////////////////

function split_hrs($hours, $class, $color = "") {
	$split = explode(".", $hours);
	$html = "<td align=\"right\" class=\"" . $class . "\"";
	if ($color) {
		$html .= " bgcolor=\"" . $color . "\"";
	}
	$html .= ">" . $split[0] . "</td><td width=\"5\" align=\"right\" class=\"" . $class . "\"";
	if ($color) {
		$html .= " bgcolor=\"" . $color . "\"";
	}
	$html .= ">" . ($split[1] ? $split[1] : "0") . "</td>";

	return $html;
}

////////////////////////////////////////////////////////////////////////////////

function flights_total($search) {
	global $all_flights;
	return row_total($search, $all_flights);
}

////////////////////////////////////////////////////////////////////////////////

function page_total($search) {
	global $page_flights;
	return row_total($search, $page_flights);
}

////////////////////////////////////////////////////////////////////////////////

function row_total($search, $db_array) {
	$total= 0.0;
	foreach ($db_array as $flight) {
		foreach ($flight as $name => $value) {
			if ($name == $search) {
				$total += $value;
			}
		}
	}
	return $total;
}
?>