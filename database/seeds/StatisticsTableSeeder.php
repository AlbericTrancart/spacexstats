<?php
use Illuminate\Database\Seeder;
use SpaceXStats\Models\Statistic;

class StatisticsTableSeeder extends Seeder {
    public function run() {

        // Next launch
        Statistic::create(array(
            'order' => 1,
            'type' => 'Next Launch',
            'name' => 'Next Launch',
            'description' => '{{ nextLaunchSummary }}',
            'display' => 'mission'
        ));

        // Launch Counts
        Statistic::create(array(
            'order' => 2,
            'type' => 'Launch Count',
            'name' => 'Total',
            'description' => 'As of {{ currentMonth }}, SpaceX has launched {{ totalRockets }} rockets, carrying a variety of payloads to multiple destinations; including LEO, GTO, L1, and the ISS. SpaceX currently has a manifest of over 70 flights that will fly over the coming years.',
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 3,
            'type' => 'Launch Count',
            'name' => 'Falcon 9',
            'description' => "Nearly 3/4's the height of the Saturn V, yet thinner than a Space Shuttle SRB, Falcon 9 is the workhorse of SpaceX's rocket fleet. Able to carry 13,150kg to LEO and 4,850kg to GTO with first stage reusability. It has launched {{ launchCount }} times and is on its third iteration (Falcon 9 v1.2)",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 4,
            'type' => 'Launch Count',
            'name' => 'Falcon Heavy',
            'description' => "When Falcon Heavy launches in 2016, it will become the world's most powerful rocket, able to carry over 53 metric tonnes to Low Earth Orbit in full expendable mode, rising on its 27 first stage Merlin 1D engines. Only the mighty Saturn V has delivered more payload to orbit.",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 5,
            'type' => 'Launch Count',
            'name' => 'Falcon 1',
            'description' => "Falcon 1 was SpaceX's original two stage rocket - the first stage equipped with a single Merlin 1A engine, and later, the venerable Merlin 1C. Launched exclusively from Kwajalein, it was able to lift 670kg to LEO and became the first privately-developed rocket to reach Earth orbit. It launched 5 times over approximately 2 years.",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 6,
            'type' => 'Launch Count',
            'name' => 'BFR',
            'description' => "Let's not sidestep around the name. BFR is going to be a big fucking rocket. With very preliminary measurements placing it as taller than the Saturn V, 15 metres wide, and carrying over 200t to Low Earth Orbit; this will be the single largest rocket ever designed, developed and then built.",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        // Launches per year
        Statistic::create(array(
            'order' => 7,
            'type' => 'Launches Per Year',
            'name' => 'Launches Per Year',
            'description' => "With an ever-increasing launch cadence, SpaceX is on track to equal or surpass other launch providers by annual vehicles launched in 2016, and continues, nearly year-on-year to set vehicle flight records.",
            'display' => 'barchart'
        ));

        // Dragon
        Statistic::create(array(
            'order' => 8,
            'type' => 'Dragon',
            'name' => 'Missions',
            'description' => "Dragon is SpaceX's orbital spacecraft, and has flown {{ missionCount }} times atop of a Falcon 9 rocket. In December 2010, Dragon became the first privately developed spacecraft to be successfully recovered from orbit. Dragon 2 extends Dragon's ability to carry not only cargo, but crew too.",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 9,
            'type' => 'Dragon',
            'name' => 'ISS Resupplies',
            'description' => "Dragon has flown {{ issResupplyCount }} times to the ISS under NASA's Commercial Resupply Services Program, as part of a now 15-long mission contract to ferry cargo and supplies to and from the ISS.",
            'unit' => json_encode('Flights'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 10,
            'type' => 'Dragon',
            'name' => 'Total Flight Time',
            'description' => "Dragon has launched on increasing lengths of time into Low Earth orbit, and in future years, will exceed the time spent in orbit of the Space Shuttle - becoming America's longest serving spacecraft measured by time in orbit.",
            'display' => 'interval'
        ));

        Statistic::create(array(
            'order' => 11,
            'type' => 'Dragon',
            'name' => 'Flight Time',
            'description' => "Shown above is a graph plotting individual mission flight time per each Dragon mission. Each vehicle stays berthed to the ISS for approximately 30 days, with crewed vehicles staying for up to 6 months.",
            'display' => 'barchart'
        ));

        Statistic::create(array(
            'order' => 12,
            'type' => 'Dragon',
            'name' => 'Cargo',
            'description' => "Dragon remains the only spacecraft in service capable of returning significant quantities of cargo from the Station to Earth - up to 6 tonnes up and 3 tonnes down.",
            'unit' => json_encode(['Kilograms Up', 'Kilograms Down']),
            'display' => 'double'
        ));

        Statistic::create(array(
            'order' => 13,
            'type' => 'Dragon',
            'name' => 'Reflights',
            'description' => "Starting with CRS-11, SpaceX will move to reflying previously flown Dragons as a measure to reduce costs even further. This will see Dragon 1 pressure vessel production wind down.",
            'unit' => json_encode('Reflights'),
            'display' => 'single'
        ));

        // Vehicles
        Statistic::create(array(
            'order' => 14,
            'type' => 'Vehicles',
            'name' => 'Landed',
            'description' => "For SpaceX to succeed at reducing the cost of getting payload to orbit, reusability of launch vehicles is imperative. The first phase of this involves returning the first stage of the rocket back safely to Earth intact - an incredibly difficult task involving a combination of three burns that must be executed perfectly.",
            'unit' => json_encode('Landed'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 15,
            'type' => 'Vehicles',
            'name' => 'Reflown',
            'description' => "Once on the ground, the booster must be able to be refurbished and reflown in minimal time and with minimal cost. Only then can they be reflown, reducing launch costs significantly.",
            'unit' => json_encode('Reflown'),
            'display' => 'single'
        ));

        // Engines
        Statistic::create(array(
            'order' => 16,
            'type' => 'Engines',
            'name' => 'Flown',
            'description' => "SpaceX have accumulated a total of {{ engineCount }} first stage Merlin 1D engines flights across {{ flightCount }} launches. One of the best-performing rocket engines in the world, it uses a mixture of RP-1 (Kerosene) and cryogenic Liquid Oxygen (LOX), it achieves a thrust to weight ratio exceeding 150, the highest of any kerolox engine, while delivering over 825 kN of thrust.",
            'unit' => json_encode('Flown'),
            'display' => 'single'
        ));

        /*Statistic::create(array(
            'order' => 17,
            'type' => 'Engines',
            'name' => 'M1D Flight Time',
            'description' => "Because each Falcon 9 flight has 9 engines, every first stage flight represents a nominal 24 minutes of powered engine time. This rapidly builds up engine flight heritage - and makes the Merlin 1D possibly the longest-fired rocket engine in the world.",
            'display' => 'interval'
        ));*/

        Statistic::create(array(
            'order' => 18,
            'type' => 'Engines',
            'name' => 'M1D Success Rate',
            'description' => "And because of this flight heritage, a Merlin 1D engine has never failed in flight, yielding a perfect 100% success rate.",
            'unit' => json_encode('Percent'),
            'display' => 'single'
        ));

        // Launch Sites
        Statistic::create(array(
            'order' => 19,
            'type' => 'Cape Canaveral',
            'name' => 'Launches',
            'description' => "Cape Canaveral Air Force Station Space Launch Complex 40 (SLC-40), is the launch site of Falcon 9 carrying Dragon towards the International Space Station, and the starting point for many satellites heading into Geostationary Earth Orbit.",
            'unit' => json_encode('Launches'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 20,
            'type' => 'KSC',
            'name' => 'Launches',
            'description' => "In April 2014, SpaceX signed an agreement with NASA for a 20 year lease on the historic Pad 39A at Kennedy Space Center. Since then, SpaceX has constructed a horizontal integration hangar capable of holding up to 5 Falcon cores. It will see its first launch, of Falcon Heavy, in 2016.",
            'unit' => json_encode('Launches'),
            'display' => 'single'
        ));


        Statistic::create(array(
            'order' => 21,
            'type' => 'Vandenberg',
            'name' => 'Launches',
            'description' => "Vandenberg AFB Space Launch Complex 4E (SLC-4E) in California is SpaceX's departure point for satellites (mostly scientific and Earth observation) seeking a polar orbit around the Earth. SLC-4E was last used in 2005 for the final flight of the Titan IV rocket.",
            'unit' => json_encode('Launches'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 22,
            'type' => 'Boca Chica',
            'name' => 'Launches',
            'description' => "Boca Chica Beach, Texas is the location of SpaceX's new commercial-only private launch site designed to handle LEO & GEO launches on a tight schedule, freeing up other launch sites for other uses. It is expected to become operational in 2017 or 2018.",
            'unit' => json_encode('Launches'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 23,
            'type' => 'Kwajalein',
            'name' => 'Launches',
            'description' => "Omelek Island in Kwajalein Atoll was SpaceX's first launch site, used exclusively to launch the Falcon 1. Surrounded by vast amounts of sea and open ocean, making it the perfect site to launch untested demonstration rockets. Ironically, this climate also led to the failure of the first Falcon 1 launch, during which the engine failed 25 seconds into flight due to a corroded bolt.",
            'unit' => json_encode('Launches'),
            'display' => 'single'
        ));

        // DragonRiders
        Statistic::create(array(
            'order' => 24,
            'type' => 'DragonRiders',
            'name' => 'In Space',
            'description' => "No SpaceX astronauts are in orbit at this time. Dragon 2, being developed as part of NASA's Commercial Crew Transportation Capability (CCtCap) program, has already performed a pad abort test and will first fly to orbit in 2016.",
            'unit' => json_encode('DragonRiders'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 25,
            'type' => 'DragonRiders',
            'name' => 'Cumulative',
            'description' => "No SpaceX astronauts have flown yet. Dragon 2, being developed as part of NASA's Commercial Crew Transportation Capability (CCtCap) program, has already performed a pad abort test and will first fly to orbit in 2016.",
            'unit' => json_encode('DragonRiders'),
            'display' => 'single'
        ));

        // Elon Musk's Mars bet expires
        Statistic::create(array(
            'order' => 26,
            'type' => "Elon Musk's Bet Expires",
            'name' => "Elon Musk's Bet Expires",
            'description' => 'In April 2009, Michael S. Malone revealed, while interviewing Elon Musk, that the two had a bet that SpaceX would put a man on Mars by "2020 or 2025". Musk has continued to reiterate this rough timeframe since. This countdown clock expires on 1 January 2026, at 00:00 UTC. No pressure, Elon.',
            'display' => 'count'
        ));

        // Days since SpaceX founding
        Statistic::create([
            'order' => 27,
            'type' => 'Time Since Founding',
            'name' => 'Time Since Founding',
            'description' => 'SpaceX was incorporated on March 14, 2002, with their headquarters at a hotel, in downtown Los Angeles. By the end of the year, they were 14 employees strong. Their second facility was an enormous warehouse in El Segundo, where they built the Falcon 1. When they outgrew that, they moved to their current facility in Hawthorne.',
            'display' => 'count'
        ]);

        // Payloads
        Statistic::create(array(
            'order' => 28,
            'type' => "Payloads",
            'name' => 'Satellites Launched',
            'description' => 'SpaceX has launched {{ satelliteCount }} satellites in total for many different customers.',
            'unit' => json_encode(['Primary', 'Total']),
            'display' => 'double'
        ));

        Statistic::create(array(
            'order' => 29,
            'type' => "Payloads",
            'name' => 'Total Mass',
            'description' => 'These satellites can have a variety of masses, from the smallest cubesats which can weigh less than 1 kilogram, to huge comsats over 5 tonnes.',
            'unit' => json_encode('Kilograms'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 30,
            'type' => "Payloads",
            'name' => 'Mass to GTO',
            'description' => 'Geostationary Orbit serves as the nest for heavy communications satellites, where they can orbit the Earth at the same speed as the Earth rotates. As the demand for more bandwidth grows and connectiveness increases globally, launches to GTO will become more frequent.',
            'unit' => json_encode('Kilograms'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 31,
            'type' => "Payloads",
            'name' => 'Heaviest Satellite',
            'description' => '{{ heaviestSatellite }}, launched for {{ heaviestOperator }} represents the heaviest satellite SpaceX has lofted into orbit.',
            'unit' => json_encode('Kilograms'),
            'display' => 'single'
        ));

        // Upper Stages
        /*Statistic::create(array(
            'order' => 32,
            'type' => "Upper Stages",
            'name' => 'In Orbit',
            'description' => 'After satellite separation and mission completion, depending on the location and orbit of the upper stage, it can either be deorbited or left in orbit to decay naturally.',
            'unit' => json_encode('Upper Stages'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 33,
            'type' => "Upper Stages",
            'name' => "TLEs",
            'description' => 'A Two Line Element is a set of ephemeris data generated by USSTRATCOM that can be used to track objects in orbit. Published every few days, many objects in orbit will eventually accumulate thousands of entries.',
            'unit' => json_encode('Two Line Elements'),
            'display' => 'single'
        ));

        // Distance
        Statistic::create(array(
            'order' => 34,
            'type' => "Distance",
            'name' => 'Earth Orbit',
            'description' => 'On {{ distanceRecordDate }}, SpaceX hardware achieved its farthest distance from Earth ever when the Falcon 9 Upper Stage for the {{ distanceRecordMission }} mission was boosted into an orbit with an apogee of {{ distanceRecord }} kilometres.',
            'unit' => json_encode('Kilometres'),
            'display' => 'single'
        ));

        Statistic::create(array(
            'order' => 35,
            'type' => "Distance",
            'name' => 'Solar System',
            'description' => 'SpaceX hardware has not yet flown beyond Earth orbit!',
            'unit' => json_encode('Kilometres'),
            'display' => 'single'
        ));*/

        // Turnaround time
        Statistic::create(array(
            'order' => 36,
            'type' => 'Turnarounds',
            'name' => 'Quickest',
            'description' => "",
            'display' => 'interval'
        ));

        Statistic::create(array(
            'order' => 37,
            'type' => 'Turnarounds',
            'name' => 'Since Last Launch',
            'description' => "",
            'display' => 'count'
        ));

        /*Statistic::create(array(
            'order' => 38,
            'type' => 'Turnarounds',
            'name' => 'Cumulative',
            'description' => "",
            'display' => 'linechart'
        ));*/

        // Internet Constellaiton
        Statistic::create(array(
            'order' => 39,
            'type' => 'Internet Constellation',
            'name' => 'Internet Constellation',
            'description' => "SpaceX's constellation of satellites will provide high speed internet anywhere on the globe. Built in Seattle, they will be launched from a variety of locations, potentially allowing Falcon to become the most launched rocket in history.",
            "unit" => json_encode('Total Launched'), // 0/4025
            'display' => 'single'
        ));

        // Mars Population Count
        Statistic::create(array(
            'order' => 40,
            'type' => 'Mars Population Count',
            'name' => 'Mars Population Count',
            'description' => "No one's there yet ;)",
            "unit" => json_encode('People'),
            'display' => 'single'
        ));

        // Hours worked
        Statistic::create(array(
            'order' => 41,
            'type' => 'Hours Worked',
            'name' => 'Hours Worked',
            'description' => "Since 14 March 2002, thousands of SpaceX employees and Elon Musk have worked tirelessly to push the boundaries of engineering and technology, ultimately providing humanity with cheaper, faster, more reliable access to space. Thank you.",
            'display' => 'gesture'
        ));
    }
}
