<?php
namespace SpaceXStats\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use SpaceXStats\Library\Enums\Destination;
use SpaceXStats\Library\Enums\Engine;
use SpaceXStats\Library\Enums\MissionStatus;
use SpaceXStats\Models\Mission;
use SpaceXStats\Models\PartFlight;
use SpaceXStats\Models\Payload;
use SpaceXStats\Models\Spacecraft;
use SpaceXStats\Models\SpacecraftFlight;
use SpaceXStats\Models\Vehicle;
use Illuminate\Support\Facades\Log;

class StatisticResultBuilder {
	/**
	 * @return mixed
     */
	public static function nextLaunch() {
        return Cache::remember('stats:nextLaunch', 60, function() {
            return Mission::future(1)->first()->toArray();
        });
	}

	/**
	 * Fetch the total launch count for either all missions, or just a particular vehicle type.
	 *
	 * @param string $substatistic	What substatistic are we querying for?
	 * @return int
     */
	public static function launchCount($substatistic) {
		if ($substatistic === 'Total') {
            return Cache::remember('stats:launchCount:total', 60, function() {
                return Mission::whereComplete()->count();
            });
		}

        if ($substatistic === 'Falcon 1') {
            return Cache::remember('stats:launchCount:falcon1', 60, function() {
                return Mission::whereComplete()->whereGenericVehicle('Falcon 1')->count();
            });
        }

        if ($substatistic === 'Falcon 9') {
            return Cache::remember('stats:launchCount:falcon9', 60, function() {
                return Mission::whereComplete()->whereGenericVehicle('Falcon 9')->count();
            });
        }

        if ($substatistic === 'Falcon Heavy') {
            return Cache::remember('stats:launchCount:falconheavy', 60, function() {
                return Mission::whereComplete()->whereGenericVehicle('Falcon Heavy')->count();
            });
        }

        if ($substatistic === 'BFR' || $substatistic === 'ITS') {
            return Cache::remember('stats:launchCount:bfr', 60, function() {
                return Mission::whereComplete()->whereGenericVehicle('BFR')->count();
            });
        }
	}

	/**
	 * @return mixed
     */
	public static function launchesPerYear() {
		return Cache::remember('stats:launchesPerYear', 60, function() {
            return [
                'values' => DB::SELECT(DB::raw('SELECT IFNULL(launches.launches, 0) as launches, years.year 
                    from year_list years
                    left outer join (SELECT COUNT(mission_id) AS launches, YEAR(launch_exact) AS year from Missions where status = \'Complete\' group by year) AS launches on launches.year = years.year
                    where years.year BETWEEN (select MIN(YEAR(launch_exact)) from missions where status = \'Complete\') AND (select MAX(YEAR(launch_exact)) from missions where status = \'Complete\')')),
                'extrapolation' => false,
                'xAxis' => [
                    'type' => 'ordinal',
                    'key' => 'year',
                    'title' => 'Year'
                ],
                'yAxis' => [
                    'type' => 'linear',
                    'key' => 'launches',
                    'title' => 'Launches',
                    'zeroing' => true
                ]
            ];
		});
    }

	/**
	 * @param $substatistic
	 * @return mixed
     */
	public static function dragon($substatistic) {
		if ($substatistic === 'Missions') {
            return Cache::remember('stats:dragon:missions', 60, function() {
                return SpacecraftFlight::whereHas('mission', function($q) {
                    $q->whereComplete();
                })->count();
            });
		}

        if ($substatistic === 'ISS Resupplies') {
            return Cache::remember('stats:dragon:ISSResupplies', 60, function() {
                return SpacecraftFlight::whereNotNull('iss_berth')->whereHas('mission', function($q) {
                    $q->whereComplete();
                })->count();
            });
		}

        if ($substatistic === 'Total Flight Time') {
            return Cache::remember('stats:dragon:totalflighttime', 60, function() {
                return DB::table('spacecraft_flights_pivot')
                    ->selectRaw('SUM(TIMESTAMPDIFF(SECOND,missions.launch_exact,spacecraft_flights_pivot.end_of_mission)) AS duration')
                    ->where('missions.status','Complete')
                    ->join('missions','missions.mission_id','=','spacecraft_flights_pivot.mission_id')
                    ->first()->duration;
            });
		}

        if ($substatistic === 'Flight Time') {
            return Cache::remember('stats:dragon:flighttime', 60, function() {
                return [
                    'values' => SpacecraftFlight::selectRaw('TIMESTAMPDIFF(SECOND,missions.launch_exact,spacecraft_flights_pivot.end_of_mission) / 86400 AS duration, missions.name AS mission')
                        ->where('missions.status','Complete')
                        ->join('missions','missions.mission_id','=','spacecraft_flights_pivot.mission_id')->get(),
                    'extrapolation' => false,
                    'xAxis' => [
                        'type' => 'ordinal',
                        'key' => 'mission',
                        'title' => 'Mission'
                    ],
                    'yAxis' => [
                        'type' => 'linear',
                        'key' => 'duration',
                        'title' => 'Duration (Days)',
                        'zeroing' => true
                    ]
                ];
            });
		}

        if ($substatistic === 'Cargo') {
            $query = Cache::remember('stats:dragon:cargo', 60, function() {
                return SpacecraftFlight::select(DB::raw('SUM(upmass) AS upmass, SUM(downmass) AS downmass'))->whereHas('mission', function($q) {
                    $q->whereComplete();
                })->first();
            });

            $stat[0] = number_format($query->upmass);
            $stat[1] = number_format($query->downmass);

            return $stat;

		}

        if ($substatistic === 'Reflights') {
            return Cache::remember('stats:dragon:reflights', 60, function() {
                return DB::select(DB::raw("SELECT COALESCE(SUM(reflights), 0) as total_flights FROM (SELECT COUNT(*)-1 as reflights FROM spacecraft JOIN spacecraft_flights_pivot ON spacecraft.spacecraft_id = spacecraft_flights_pivot.spacecraft_id WHERE spacecraft.spacecraft_id=spacecraft_flights_pivot.spacecraft_id GROUP BY spacecraft_flights_pivot.spacecraft_id HAVING reflights > 0) reflights"))[0]->total_flights;
            });
        }
	}

	/**
	 * @param $substatistic
	 * @return mixed
     */
	public static function vehicles($substatistic) {
        if ($substatistic == 'Landed') {
            return Cache::remember('stats:vehicles:landed', 60, function() {
                return PartFlight::where('landed', true)->count();
            });
        }

        if ($substatistic == 'Reflown') {
            return Cache::remember('stats:vehicles:reflown', 60, function() {
                return DB::select(DB::raw("SELECT COALESCE(SUM(reflights), 0) as total_flights FROM (SELECT COUNT(*)-1 as reflights FROM parts JOIN part_flights_pivot ON parts.part_id = part_flights_pivot.part_id JOIN missions on missions.mission_id = part_flights_pivot.mission_id WHERE parts.part_id=part_flights_pivot.part_id AND missions.outcome = 'Success' GROUP BY part_flights_pivot.part_id HAVING reflights > 0) reflights"))[0]->total_flights;
            });
        }
    }

	/**
	 * Fetch the number of firststage Merlin 1D engines (both normal and fullthrust) flown on operational missions
	 *
	 * @param $substatistic
	 * @return int
     */
	public static function engines($substatistic) {
        if ($substatistic === 'Flown') {
            return Cache::remember('stats:engines:flown', 60, function() {
                return PartFlight::whereIn('firststage_engine', [Engine::Merlin1D, Engine::Merlin1DFullThrust])->whereHas('mission', function($q) {
                    return $q->whereComplete();
                })->count() * 9;
            });
        }

		if ($substatistic === 'M1D Flight Time') {
            return Cache::remember('stats:engines:M1DFlightTime', 60, function() {
                return PartFlight::select(DB::raw('SUM(part_flights_pivot.firststage_meco) AS flight_time'))
                    ->whereHas('mission', function($q) {
                        $q->whereComplete();
                    })
                    ->first()->flight_time;
            });
		}

        if ($substatistic === 'M1D Success Rate') {
            return Cache::remember('status:engines:M1DSuccessRate', 60, function() {
                return PartFlight::select(DB::raw('ROUND(100 - (SUM(part_flights_pivot.firststage_engine_failures) / (COUNT(part_flights_pivot.mission_id) * 9) * 100)) AS success_rate'))
                    ->whereHas('mission', function($query) {
                        $query->whereComplete()->whereSpecificVehicle(['Falcon 9 v1.1', 'Falcon 9 v1.2']);
                    })
                    ->first()->success_rate;
            });
		}
	}

	/**
	 * @param $substatistic
	 * @return string
     */
	public static function capeCanaveral($substatistic) {
		if ($substatistic === 'Launches') {
            return Cache::remember('stats:capeCanaveral:launches', 60, function() {
                return Mission::whereComplete()->whereHas('launchSite', function($q) {
                    $q->where('name','SLC-40');
                })->count();
            });

		} else if ($substatistic === 'Last Launch') {
			try {
				$lastLaunch = Mission::lastFromLaunchSite('SLC-40')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $lastLaunch;

		} else if ($substatistic === 'Next Launch') {
			try {
				$nextLaunch = Mission::nextFromLaunchSite('SLC-40')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $nextLaunch;
		}
	}

	/**
	 * @param $substatistic
	 * @return string
     */
	public static function kSC($substatistic) {
        if ($substatistic === 'Launches') {
            return Cache::remember('stats:ksc:launches', 60, function() {
                return Mission::whereComplete()->whereHas('launchSite', function($q) {
                    $q->where('name','LC-39A');
                })->count();
            });

        } else if ($substatistic === 'Last Launch') {
            try {
                $lastLaunch = Mission::lastFromLaunchSite('LC-39A')->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return false;
            }
            return $lastLaunch;

        } else if ($substatistic === 'Next Launch') {
            try {
                $nextLaunch = Mission::nextFromLaunchSite('LC-39A')->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return false;
            }
            return $nextLaunch;
        }
    }

	/**
	 * @param $substatistic
	 * @return string
     */
	public static function vandenberg($substatistic) {
		if ($substatistic === 'Launches') {
            return Cache::remember('stats:vandenberg:launches', 60, function() {
                return Mission::whereComplete()->whereHas('launchSite', function($q) {
                    $q->where('name','SLC-4E');
                })->count();
            });

		} else if ($substatistic === 'Last Launch') {
			try {
				$lastLaunch = Mission::lastFromLaunchSite('SLC-4E')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $lastLaunch;

		} else if ($substatistic === 'Next Launch') {
			try {
				$nextLaunch = Mission::nextFromLaunchSite('SLC-4E')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $nextLaunch;
		}
	}

	/**
	 * @param $substatistic
	 * @return string
     */
	public static function bocaChica($substatistic) {
		if ($substatistic === 'Launches') {
            return Cache::remember('stats:bocaChica:launches', 60, function() {
                return Mission::whereComplete()->whereHas('launchSite', function($q) {
                    $q->where('name','Boca Chica');
                })->count();
            });

		} else if ($substatistic === 'Last Launch') {
			try {
				$lastLaunch = Mission::lastFromLaunchSite('Boca Chica')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $lastLaunch;

		} else if ($substatistic === 'Next Launch') {
			try {
				$nextLaunch = Mission::nextFromLaunchSite('Boca Chica')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $nextLaunch;
		}
	}

	/**
	 * @param $substatistic
	 * @return string
     */
	public static function kwajalein($substatistic) {
		if ($substatistic === 'Launches') {
            return Cache::remember('stats:kwajalein:launches', 60, function() {
                return Mission::whereComplete()->whereHas('launchSite', function($q) {
                    $q->where('name','Omelek Island');
                })->count();
            });

		} else if ($substatistic === 'Last Launch') {
			try {
				$lastLaunch = Mission::lastFromLaunchSite('Omelek Island')->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return false;
			}
			return $lastLaunch;
		}
	}

	/**
	 * @param $substatistic
	 * @return int
     */
	public static function dragonRiders($substatistic) {
        if ($substatistic == 'In Space') {
            return Cache::remember('stats:dragonRiders:inSpace', 60, function() {
                return DB::table('astronauts_flights_pivot')
                    ->join('spacecraft_flights_pivot', 'spacecraft_flights_pivot.spacecraft_flight_id','=','astronauts_flights_pivot.spacecraft_flight_id')
                    ->join('missions', 'missions.mission_id', '=', 'spacecraft_flights_pivot.mission_id')
                    ->where('missions.status', MissionStatus::InProgress)
                    ->count();
            });

		} else if ($substatistic == 'Cumulative') {
            return Cache::remember('stats:dragonRiders:cumulative', 60, function() {
                return DB::table('astronauts_flights_pivot')
                    ->join('spacecraft_flights_pivot', 'spacecraft_flights_pivot.spacecraft_flight_id','=','astronauts_flights_pivot.spacecraft_flight_id')
                    ->join('missions', 'missions.mission_id', '=', 'spacecraft_flights_pivot.mission_id')
                    ->where('missions.status', MissionStatus::InProgress)
                    ->orWhere('missions.status', MissionStatus::Complete)
                    ->count();
            });
		}
    }

	/**
	 * @return int
     */
	public static function elonMusksBetExpires() {
        return Cache::remember('stats:elonMusksBetExpires', 60, function() {
            return Carbon::create(2026, 1, 1, 0, 0, 0)->toDateTimeString();
        });

    }

    public static function timeSinceFounding() {
        return Cache::remember('stats:timeSinceFounding', 60, function() {
            return Carbon::create(2002, 3, 14, 0, 0, 0)->toDateTimeString();
        });
    }

	/**
	 * @param $substatistic
	 * @return int
     */
	public static function payloads($substatistic) {
        if ($substatistic == 'Satellites Launched') {

            return Cache::remember('stats:payloads:satellitesLaunched', 60, function() {
                $payloads = Payload::whereHas('mission', function($q) {
                    $q->where('status', MissionStatus::Complete);
                })->get();

                $stat[1] = $payloads->count();
                $stat[0] = $payloads->filter(function($payload) {
                    return $payload->primary;
                })->count();

                return $stat;
            });


		} else if ($substatistic == 'Total Mass') {
            return Cache::remember('stats:payloads:totalMass', 60, function() {
                return number_format(round(Payload::whereHas('mission', function($q) {
                    $q->whereComplete();
                })->sum('mass')));

            });

		} else if ($substatistic == 'Mass to GTO') {
            return Cache::remember('stats:payloads:massToGTO', 60, function() {
                return number_format(round(Payload::whereHas('mission', function($q) {
                    $q->whereComplete()->whereHas('destination', function($q) {
                        $q->whereIn('destination', [Destination::GeostationaryTransferOrbit, Destination::SupersynchronousGTO, Destination::SubsynchronousGTO]);
                    });
                })->sum('mass')));

            });

		} else if ($substatistic == 'Heaviest Satellite') {
            return Cache::remember('stats:payloads:heaviestSatellite', 60, function() {
                return number_format(round(Payload::whereHas('mission', function($q) {
                    $q->whereComplete();
                })->max('mass')));
            });
		}
    }

    /**
     * @param $substatistic
     * @return int
     */
	public static function upperStages($substatistic) {
		if ($substatistic == 'In Orbit') {
            return PartFlight::where('upperstage_status', 'In Orbit')->count();

		} else if ($substatistic == 'TLEs') {
			return DB::table('orbital_elements')->count();
		}
    }

	/**
	 * @param $substatistic
	 * @return int
     */
	public static function distance($substatistic) {
		if ($substatistic == 'Earth Orbit') {
			return round(DB::table('orbital_elements')->max('apogee'));
		} else if ($substatistic == 'Solar System') {
			return 0;
		}
    }

	/**
	 * @param $substatistic
	 * @return int
     */
	public static function turnarounds($substatistic) {
        if ($substatistic == 'Quickest') {

            $lowestTurnaround = null;
            $missions = Mission::past()->get()->keyBy('launch_order_id');

            $missions->each(function($mission, $key) use ($missions, &$lowestTurnaround) {
                if ($key == 1) {
                    return null;
                }

                $turnaround = Carbon::parse($mission->launch_exact)->diffInSeconds(Carbon::parse($missions->get($key-1)->launch_exact));
                $lowestTurnaround = $lowestTurnaround == null ? $turnaround : min($lowestTurnaround, $turnaround);
            });

            return $lowestTurnaround;

		} else if ($substatistic == 'Since Last Launch') {
			return Mission::past()->first()->launch_date_time;

		} 
		else if ($substatistic == 'Over Time') {
		} 
		else if ($substatistic == 'LC-39A') {
            $lowestTurnaround = null;
			$previousMission = null;
            $missions = Mission::past()
			->where('locations.name', 'LC-39A')
			->join('locations', 'locations.location_id','=','missions.launch_site_id')
			->orderBy('launch_exact', 'asc')
			->get();
            
            
            //$missions->each(function($mission, $key) use ($missions, &$lowestTurnaround) {
			$missions->each(function($mission) use ($missions, &$lowestTurnaround, &$previousMission) {
                if ($previousMission == null) {
					$previousMission = $mission;
                    return null;
                }

                $turnaround = Carbon::parse($mission->launch_exact)->diffInSeconds(Carbon::parse($previousMission->launch_exact));
                $lowestTurnaround = $lowestTurnaround == null ? $turnaround : min($lowestTurnaround, $turnaround);
				
				$previousMission = $mission;
            });

            return $lowestTurnaround;
		}
		else if ($substatistic == 'SLC-4E') {
            $lowestTurnaround = null;
			$previousMission = null;
            $missions = Mission::past()
			->where('locations.name', 'SLC-4E')
			->join('locations', 'locations.location_id','=','missions.launch_site_id')
			->orderBy('launch_exact', 'asc')
			->get();
            
            
            //$missions->each(function($mission, $key) use ($missions, &$lowestTurnaround) {
			$missions->each(function($mission) use ($missions, &$lowestTurnaround, &$previousMission) {
                if ($previousMission == null) {
					$previousMission = $mission;
                    return null;
                }

                $turnaround = Carbon::parse($mission->launch_exact)->diffInSeconds(Carbon::parse($previousMission->launch_exact));
                $lowestTurnaround = $lowestTurnaround == null ? $turnaround : min($lowestTurnaround, $turnaround);
				
				$previousMission = $mission;
            });

            return $lowestTurnaround;
		}
		else if ($substatistic == 'SLC-40') {
            $lowestTurnaround = null;
			$previousMission = null;
            $missions = Mission::past()
			->where('locations.name', 'SLC-40')
			->join('locations', 'locations.location_id','=','missions.launch_site_id')
			->orderBy('launch_exact', 'asc')
			->get();
            
            
            //$missions->each(function($mission, $key) use ($missions, &$lowestTurnaround) {
			$missions->each(function($mission) use ($missions, &$lowestTurnaround, &$previousMission) {
                if ($previousMission == null) {
					$previousMission = $mission;
                    return null;
                }

                $turnaround = Carbon::parse($mission->launch_exact)->diffInSeconds(Carbon::parse($previousMission->launch_exact));
                $lowestTurnaround = $lowestTurnaround == null ? $turnaround : min($lowestTurnaround, $turnaround);
				
				$previousMission = $mission;
            });

            return $lowestTurnaround;
		}
    }

	/**
	 * Retrieve the number of SpaceX satellites that have been launched to support their internet constellation plans.
	 *
	 * @return int
     */
	public static function internetConstellation() {
        return 0; // We can safely return 0 for a while
    }

	/**
	 * Return the current population of Mars.
	 *
	 * @return int
     */
	public static function marsPopulationCount() {
        return 0; // We can safely return 0 for a few years at least
    }

	/**
	 * Retrieve the number of hours SpaceX employees have worked.
	 *
	 * @return int
     */
	public static function hoursWorked() {
        return 'countless'; // It's true
    }
}
?>
