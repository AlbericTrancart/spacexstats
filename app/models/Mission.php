<?php
namespace SpaceXStats\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use SpaceXStats\Library\Enums\LaunchSpecificity;
use SpaceXStats\Library\Enums\MissionControlType;
use SpaceXStats\Library\Enums\MissionOutcome;
use SpaceXStats\Library\Enums\MissionStatus;
use SpaceXStats\Library\Launch\LaunchDateTimeResolver;
use SpaceXStats\Library\Launch\LaunchReorderer;
use SpaceXStats\Mail\MailQueues\MissionMailQueue;
use SpaceXStats\Presenters\MissionPresenter;
use SpaceXStats\Presenters\Traits\PresentableTrait;
use SpaceXStats\Validators\ValidatableTrait;
use Parsedown;

class Mission extends Model {

	use PresentableTrait, ValidatableTrait;

	protected $table = 'missions';
	protected $primaryKey = 'mission_id';
    public $timestamps = true;

    protected $hidden = [];
    protected $appends = ['launch_date_time'];
    protected $fillable = [];
    protected $guarded = [];

    protected $presenter = MissionPresenter::class;

    // Observers
    public static function boot() {
        parent::boot();

        $missionMailQueuer = new MissionMailQueue();

        Mission::created(function($mission) use ($missionMailQueuer) {
            // Add emails to queue
            $missionMailQueuer->newMission($mission);

            // Add to RSS

            // Tweet about it
        });

        Mission::updating(function($mission) use ($missionMailQueuer) {
            // If  the launch date time has changed
            /*if ($) {
                // Send out a new email
                $missionMailQueuer->LaunchChange($mission);

                // Add to RSS

                // Tweet about it
            }*/
        });
    }

    // Validation
    public $rules = array(
        'name' => ['sometimes', 'required', 'string', 'varchar:tiny'],
        'contractor' => ['sometimes', 'required', 'string', 'varchar:small'],
        'launch_exact' => ['sometimes', 'date_format:Y-m-d H:i:s'],
        'launch_approximate' => ['sometimes', 'string', 'varchar:tiny']
    );

    public $messages = array(
        'name.varchar' => 'The mission name needs to be shorter than :size characters'
    );

	// Relations
	public function vehicle() {
		return $this->belongsTo('SpaceXStats\Models\Vehicle');
	}

    public function parts() {
        return $this->belongsToMany('SpaceXStats\Models\Part', 'part_flights_pivot');
    }

    public function partFlights() {
        return $this->hasMany('SpaceXStats\Models\PartFlight');
    }

	public function spacecraftFlight() {
		return $this->hasOne('SpaceXStats\Models\SpacecraftFlight');
	}

    public function astronautFlights() {
        return $this->hasManyThrough('SpaceXStats\Models\AstronautFlight', 'SpacecraftFlight');
    }

	public function prelaunchEvents() {
		return $this->hasMany('SpaceXStats\Models\PrelaunchEvent');
	}

    public function destination() {
        return $this->belongsTo('SpaceXStats\Models\Destination');
    }

    public function missionType() {
        return $this->belongsTo('SpaceXStats\Models\MissionType');
    }

    public function objects() {
        return $this->hasMany('SpaceXStats\Models\Object');
    }

    public function payloads() {
        return $this->hasMany('SpaceXStats\Models\Payload');
    }

    public function telemetry() {
        return $this->hasMany('SpaceXStats\Models\Telemetry');
    }

    public function orbitalElements() {
        return $this->hasManyThrough('SpaceXStats\Models\OrbitalElement', 'SpaceXStats\Models\PartFlight');
    }

    // Conditional Relationships
    public function launchSite() {
        return $this->belongsTo('SpaceXStats\Models\Location', 'launch_site_id');
    }

    public function firstStage() {
        return $this->hasOne('SpaceXStats\Models\PartFlight')->whereHas('part', function($q) {
            $q->where('type', 'First Stage');
        });
    }

    public function upperStage() {
        return $this->hasOne('SpaceXStats\Models\PartFlight')->whereHas('part', function($q) {
            $q->where('type', 'Upper Stage');
        });
    }

    public function positionalTelemetry() {
        return $this->hasMany('SpaceXStats\Models\Telemetry')->where(function($q) {
            $q->orWhereNotNull('velocity')->orWhereNotNull('altitude')->orWhereNotNull('downrange');
        });
    }

    public function articles() {
        return $this->hasMany('SpaceXStats\Models\Object')->where('type', MissionControlType::Article);
    }

    public function launchVideo() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'launch_video');
    }

    public function missionPatch() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'mission_patch');
    }

    public function pressKit() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'press_kit');
    }

    public function cargoManifest() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'cargo_manifest');
    }

    public function prelaunchPressConference() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'prelaunch_press_conference');
    }

    public function postlaunchPressConference() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'postlaunch_press_conference');
    }

    public function featuredImage() {
        return $this->belongsTo('SpaceXStats\Models\Object', 'featured_image');
    }

	// Attribute Accessors
	public function getLaunchDateTimeAttribute() {
		return $this->isLaunchPrecise() ? $this->launch_exact: $this->launch_approximate;
	}

	public function getLaunchProbabilityAttribute() {
        if ($this->status == MissionStatus::Upcoming) {
            $preciseEstimatedTimeOfLaunch = Carbon::instance(LaunchDateTimeResolver::parseString($this->launch_date_time)->getDateTime());
            $timeUntilLaunch = Carbon::now()->diffInSeconds($preciseEstimatedTimeOfLaunch);

            $delayedLaunches = count(DB::table('prelaunch_events')->select(['prelaunch_events.mission_id'])
                ->where('event', 'Launch Change')
                ->whereRaw('prelaunch_events.occurred_at > DATE_SUB(missions.launch_exact, INTERVAL ' . $timeUntilLaunch . ' SECOND)')
                ->where('missions.status','Complete')
                ->join('missions', 'missions.mission_id','=','prelaunch_events.mission_id')
                ->groupBy('missions.mission_id')
                ->get());

            $totalLaunches = Mission::whereComplete()->count();

            return (1 / $totalLaunches) * ($totalLaunches - $delayedLaunches);
        }
        return null;
	}

    public function getSpecificVehicleCountAttribute() {
        $self = $this;

        return Mission::where('launch_order_id','<=',$this->launch_order_id)->whereHas('vehicle', function($q) use($self) {
            $q->where('vehicle', $self->vehicle->vehicle);
        })->count();
    }

    public function getGenericVehicleCountAttribute() {
        $self = $this;

        if (strpos($this->vehicle,'Falcon 9')) {
        	return Mission::where('launch_order_id','<=',$this->launch_order_id)->whereHas('vehicle',function($q) use($self) {
        		$q->where('vehicle','LIKE','Falcon 9%');
        	})->count();
        } else {
            return Mission::where('launch_order_id','<=',$this->launch_order_id)->whereHas('vehicle', function($q) use($self) {
                $q->where('vehicle', $self->vehicle->vehicle);
            })->count();
        }
    }

    public function getLaunchOfYearAttribute() {
        // Fetch the year of the current launch
        if ($this->isLaunchPrecise()) {
            $year = Carbon::parse($this->launch_date_time)->year;
        } else {
            preg_match('/\b\d{4}\b/', $this->launch_date_time, $year);
            $year = $year[0];
        }

        // Now find all other missions with that year
        return Mission::before($this->launch_order_id)
            ->where(function($q) use ($year) {
                $q->where('launch_approximate', 'LIKE', '%'.$year.'%')
                    ->orWhere(DB::raw('YEAR(launch_exact)'), $year);
            })->count() + 1;
    }

    public function getSuccessfulConsecutiveLaunchAttribute() {
        if ($this->status == MissionStatus::Complete && $this->outcome != MissionOutcome::Failure) {

            try {
                $lastFailedMissionLaunchOrderId = Mission::where('outcome', MissionOutcome::Failure)
                    ->before($this->launch_order_id)
                    ->firstOrFail()->launch_order_id;

            } catch (ModelNotFoundException $e) {
                $lastFailedMissionLaunchOrderId = 0;
            }

            return $this->launch_order_id - $lastFailedMissionLaunchOrderId;
        }
        return null;
    }

    public function getTurnaroundTimeAttribute() {
        if ($this->status == MissionStatus::Complete) {
            $previousMission = Mission::before()->first();

            return Carbon::parse($previousMission->launch_date_time)->diffInSeconds(Carbon::parse($this->launch_date_time));
        }
        return null;
    }

    public function getPayloadMassRankingAttribute() {
        $missionsOrderedByPayloadMass = DB::table('missions')
            ->select('missions.mission_id')
            ->join('payloads', 'missions.mission_id', '=', 'payloads.mission_id')
            ->groupBy('missions.mission_id')->orderByRaw('SUM(payloads.mass) DESC')->get();

        foreach ($missionsOrderedByPayloadMass as $ranking => $mission) {
            if ($this->mission_id == $mission->mission_id) {
                return $ranking + 1;
            }
        }
        return null;
    }

    public function getArticleMdAttribute() {
        return Parsedown::instance()->text($this->attributes['article']);
    }

    // Attribute Mutators
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }

    public function setLaunchDateTimeAttribute($value) {
        // Reorder launches
        //$launchReorderer = new LaunchReorderer($this, $value);
        //$launchReorderer->run();

        // Also query an API to check the launch visibility (twilight, daytime, etc)
    }

    // Methods
    /**
     *  Checks if the current mission is the next to launch.
     *
     * @return bool     Is the launch next or not?
     */
    public function isNextToLaunch() {
        return $this->mission_id === Mission::future()->first()->mission_id;
    }

    /**
     * Checks whether the launch is precise enough to use a countdown.
     *
     * Note that this method doesn't check if the launch is precisely precise (i.e. down to the second),
     * but rather whether the launch can be expressed as a DateTime object, any mission that returns true
     * from this method will contain a launch date time at least as accurate as a day.
     *
     * @return bool     Is the launch precise or not?
     */
    public function isLaunchPrecise() {
        return $this->launch_specificity >= LaunchSpecificity::Day;
    }

	// Scoped Queries
    /**
     * @param $query
     * @param $slug
     * @return mixed
     */
    public function scopeWhereSlug($query, $slug) {
        return $query->where('slug', $slug);
    }

    /**
     * @param $query
     * @param bool|false $inclusive
     * @return mixed
     */
    public function scopeWhereComplete($query, $inclusive = false) {
        if ($inclusive) {
            return $query->where('status', MissionStatus::Complete)->orWhere('status', MissionStatus::InProgress);
        }
		return $query->where('status', MissionStatus::Complete);
	}

    /**
     * @param $query
     * @param bool|false $inclusive
     * @return mixed
     */
    public function scopeWhereUpcoming($query, $inclusive = false) {
        if ($inclusive) {
            return $query->where('status', MissionStatus::Upcoming)->orWhere('status', MissionStatus::InProgress);
        }
		return $query->where('status', MissionStatus::Upcoming);
	}

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFuture($query) {
		return $query->whereUpcoming()->orderBy('launch_order_id');
	}

    /**
     * @param $query
     * @return mixed
     */
    public function scopePast($query) {
		return $query->whereComplete()->orderBy('launch_order_id', 'desc');
	}

    public function scopeNext($query) {
        return $query->future()->first();
    }

    public function scopePrevious($query) {
        return $query->past()->first();
    }

	// Get 1 or more next launches relative to a current launch_order_id
    /**
     * @param $query
     * @param $currentLaunchOrderId
     * @return mixed
     */
    public function scopeAfter($query, $currentLaunchOrderId) {
		return $query->where('launch_order_id', '>', $currentLaunchOrderId)
						->orderBy('launch_order_id');
	}

	// Get 1 or more previous launches relative to a current launch_order_id
    /**
     * @param $query
     * @param $currentLaunchOrderId
     * @return mixed
     */
    public function scopeBefore($query, $currentLaunchOrderId) {
		return $query->where('launch_order_id', '<', $currentLaunchOrderId)
						->orderBy('launch_order_id', 'DESC');
	}

    /**
     * @param $query
     * @param $site
     * @return mixed
     */
    public function scopePastFromLaunchSite($query, $site) {
		return $query->whereComplete()->whereHas('launchSite', function($q) use($site) {
			$q->where('name',$site);
		})->orderBy('launch_order_id','DESC');
	}

    /**
     * @param $query
     * @param $site
     * @return mixed
     */
    public function scopeFutureFromLaunchSite($query, $site) {
		return $query->whereUpcoming()->whereHas('launchSite', function($q) use($site) {
			$q->where('name',$site);
		})->orderBy('launch_order_id','ASC');
	}

    /**
     * @param $query
     * @param $vehicle
     * @return mixed
     */
    public function scopeWhereSpecificVehicle($query, $vehicles) {
        return $query->whereHas('vehicle', function($q) use($vehicles) {
            if (is_array($vehicles)) {
                $q->whereIn('vehicle', $vehicles);
            } else {
                $q->where('vehicle', $vehicles);
            }
        });
    }

    /**
     * @param $query
     * @param $vehicle
     * @return mixed
     */
    public function scopeWhereGenericVehicle($query, $vehicle) {
        return $query->whereHas('vehicle', function($q) use ($vehicle) {
            $q->where('vehicle', 'like', ($vehicle == 'Falcon 9') ? $vehicle . '%' : $vehicle);
        });
    }
}