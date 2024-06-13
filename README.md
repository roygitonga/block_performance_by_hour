# Performance by Hour Block Plugin for Moodle

The `Performance by Hour` block plugin displays a bar chart showing the number of logins per hour for the currently logged-in user. The chart is generated using Chart.js and provides a visual representation of the user's login activity over the past 24 hours.

## Features

- Displays a bar chart with login counts for each hour of the day.
- Uses Chart.js for rendering the chart.
- Generates random background colors for each bar in the chart.
- Integrates seamlessly with Moodle.

## Installation

### Step 1: Download the Plugin

Download the `performance_by_hour` plugin folder from the repository.

### Step 2: Upload to Moodle

1. Upload the `performance_by_hour` folder to the `/blocks/` directory of your Moodle installation.

### Step 3: Install the Plugin

1. Log in to your Moodle site as an administrator.
2. Navigate to `Site administration` > `Notifications`.
3. Follow the on-screen instructions to complete the installation of the plugin.

### Step 4: Add the Block

1. Navigate to a course page or the site homepage.
2. Turn editing on.
3. Add the "Performance by Hour" block to the page.

## Files Included

- `block_performance_by_hour.php`: Main plugin file that contains the logic for fetching data and rendering the chart.
- `version.php`: Contains the plugin version and compatibility information.
- `lang/en/block_performance_by_hour.php`: Language strings for the plugin.

## Usage

Once the block is added to a page, it will display a bar chart with the number of logins per hour for the currently logged-in user. The chart data is fetched from the Moodle logs, and each bar is color-coded with a randomly generated color.

## Troubleshooting

If the chart does not appear or you encounter any issues, ensure the following:

1. The plugin folder is correctly placed in the `/blocks/` directory.
2. You have the necessary permissions to view logs.
3. The Chart.js library is correctly loaded.
4. There are no JavaScript errors in the browser console.

## License

This plugin is part of Moodle and is licensed under the [GNU General Public License](http://www.gnu.org/copyleft/gpl.html), either version 3 of the License, or (at your option) any later version.

## Contributing

Contributions are welcome! If you find any issues or have suggestions for improvements, please submit a pull request or open an issue on the repository.

## Credits

This plugin was developed by Roy Gitonga.
