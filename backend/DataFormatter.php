<?php
namespace RichieLennox;

/**
 * Class to handle the formatting of data from flight records.
 */
class DataFormatter
{
    protected $groups;

    public function __construct()
    {
        $this->groups = [];
    }

    /**
     * Loops over each flight record in the array.
     * Creates a list of groups based on flight tail name.
     * Collates required fields from json file and serves both arrays as json.
     *
     * @param  array $flight_records
     * @return json $resp
     */
    public function formatData($flight_records)
    {
        $formatted_records = [];
        // getting a list of unique flight tails
        $this->groups = $this->collateGroups($flight_records);
        // sorting flight_records by date
        usort($flight_records, [$this, 'sortByDate']);
        // looping through flight records and sanatising them to be used correctly
        foreach ($flight_records as $index => $flight_record) {
            $formatted_records[] = $this->sanatiseRecord(
                $index,
                $flight_record
            );
        }

        $resp = [
            "groups" => $this->sanatiseGroups(),
            "items" => $formatted_records,
            "calendarStart" => strtotime($flight_records[0]['date']),
            "calendarEnd" => strtotime($flight_records[array_key_last($flight_records)]['date']),
        ];

        return json_encode($resp);
    }

    /**
     * sorts arrays by 'date' field
     *
     * @param  array $a
     * @param  array $b
     * @return positon
     */
    private function sortByDate($a, $b)
    {
        return strcmp($a['date'], $b['date']);
    }

    /**
     * Searches array of groups by tail name.
     * Returns group ID.
     *
     * @param  string $tail
     * @return int $result
     */
    private function searchGroupsByTail($tail)
    {
        $result = array_search($tail, $this->groups);

        return $result;
    }

    /**
     * Returns a unique array of flight tail IDs.
     *
     * @param  array $records
     * @return array $records
     */
    private function collateGroups($records)
    {
        // using array_values to return a newly keyed array
        return array_values(
            array_unique(
                array_map(function ($record) {
                    return $record["tail"];
                }, $records)
            )
        );
    }

    /**
     * formats groups into multidientional array.
     * Cotains ID and title of group.
     *
     * @return array $groups
     */
    private function sanatiseGroups()
    {
        $groups = [];
        foreach ($this->groups as $index => $group) {
            $groups[] = [
                'id' => $index,
                'title' => $group,
            ];
        }

        return $groups;
    }

    /**
     * Returns a fomatted record with required fields.
     * Will also include group ID from class groups as well as unix timestamped dates.
     *
     * @param  int $index
     * @param  array $record
     * @return array $record
     */
    private function sanatiseRecord($index, $record)
    {
        // calculating minutes from departure time and arrival time
        $minutes = round(abs(strtotime($record["actual_arrival"]) - strtotime($record["actual_departure"])) / 60, 2);
        // calculating hours and minutes for formatting
        $hours = str_pad(floor($minutes / 60), 2, '0', STR_PAD_LEFT) .
        ':' . str_pad(($minutes - floor($minutes / 60) * 60), 2, '0', STR_PAD_LEFT);
        // Formatting title string, to show information on hover
        $title = "Flight ID: " . $record['flightid'] . "\n";
        $title .= "Origin: " . $record["origin"] . "\n";
        $title .= "Destination: " . $record["destination"] . "\n";
        $title .= "Tail: " . $record["tail"] . "\n";
        $title .= "Departure Time: " . date('H:i', strtotime($record["actual_departure"])) . "\n";
        $title .= "Arrival Time: " . date('H:i', strtotime($record["actual_arrival"])) . "\n";
        $title .= "Flight Time: " . $hours;
        return [
            "id" => $index,
            "group" => $this->searchGroupsByTail($record["tail"]),
            "tail" => $record["tail"],
            "start_time" => strtotime($record["actual_departure"]) * 1000, // converting to unix timestamp
            "end_time" => strtotime($record["actual_arrival"]) * 1000, // converting to unix timestamp
            'title' => $title,
            'itemProps' => [
                "flight_time" => $hours,
                "origin" => $record["origin"],
                "destination" => $record["destination"],
                "flight_id" => $record["flightid"],
                "departure_time" => date('H:i', strtotime($record["actual_departure"])),
                "arrival_time" => date('H:i', strtotime($record["actual_arrival"])),
            ],
        ];
    }
}
