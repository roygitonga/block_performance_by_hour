<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

class block_performance_by_hour extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_performance_by_hour');
    }

    public function get_content() {
        global $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        // SQL query to get current user's logins per hour
        $userLoginQuery = "
WITH Hours AS (
    SELECT '12AM' AS hour_of_day
    UNION SELECT '01AM' UNION SELECT '02AM' UNION SELECT '03AM'
    UNION SELECT '04AM' UNION SELECT '05AM' UNION SELECT '06AM'
    UNION SELECT '07AM' UNION SELECT '08AM' UNION SELECT '09AM'
    UNION SELECT '10AM' UNION SELECT '11AM' UNION SELECT '12PM'
    UNION SELECT '01PM' UNION SELECT '02PM' UNION SELECT '03PM'
    UNION SELECT '04PM' UNION SELECT '05PM' UNION SELECT '06PM'
    UNION SELECT '07PM' UNION SELECT '08PM' UNION SELECT '09PM'
    UNION SELECT '10PM' UNION SELECT '11PM'
),
-- Count logins per hour for the specified user within the last 24 hours
LoginsPerHour AS (
    SELECT
        DATE_FORMAT(FROM_UNIXTIME(timecreated), '%l%p') AS hour_of_day,
        COUNT(*) AS num_logins
    FROM
        {logstore_standard_log}
    WHERE
        userid = :userid
        AND action = 'loggedin'
        AND timecreated >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 DAY))
    GROUP BY
        hour_of_day
)
-- Left join the generated hour table with the logins per hour table to fill in zeros
SELECT
    Hours.hour_of_day,
    COALESCE(LoginsPerHour.num_logins, 0) AS num_logins
FROM
    Hours
LEFT JOIN
    LoginsPerHour ON Hours.hour_of_day = LoginsPerHour.hour_of_day
ORDER BY
    Hours.hour_of_day;
        ";

        $userLoginData = $DB->get_records_sql($userLoginQuery, ['userid' => $USER->id]);

        // Prepare data for the chart
        $chartLabels = [];
        $chartData = [];
        $backgroundColors = [];

        foreach ($userLoginData as $data) {
            $chartLabels[] = $data->hour_of_day;
            $chartData[] = $data->num_logins;
            $backgroundColors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        // Ensure there are data points for all hours (e.g., 12AM to 11PM)
        $allHours = ['12AM', '1AM', '2AM', '3AM', '4AM', '5AM', '6AM', '7AM', '8AM', '9AM', '10AM', '11AM', 
                     '12PM', '1PM', '2PM', '3PM', '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM'];
        foreach ($allHours as $hour) {
            if (!in_array($hour, $chartLabels)) {
                $chartLabels[] = $hour;
                $chartData[] = 0;
                $backgroundColors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            }
        }

        // Chart.js code to render bar chart
        $this->content = new stdClass();
        $this->content->text = '
            <div>
                <canvas id="chartId2" width="580" height="350" aria-label="chart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var ctx = document.getElementById("chartId2").getContext("2d");
                    var chartId2 = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: ' . json_encode($chartLabels) . ',
                            datasets: [{
                                label: "Logins per Hour",
                                data: ' . json_encode($chartData) . ',
                                backgroundColor: ' . json_encode($backgroundColors) . '
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: "Hour of Day"
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: "Number of Logins"
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
            </script>
        ';
        $this->content->footer = '';

        return $this->content;
    }
}

// Language strings.
$string['pluginname'] = 'Performance by Hour';
