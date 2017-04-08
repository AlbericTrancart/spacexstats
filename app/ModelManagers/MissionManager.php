<?php
namespace SpaceXStats\ModelManagers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use SpaceXStats\Models\Astronaut;
use SpaceXStats\Models\AstronautFlight;
use SpaceXStats\Models\Mission;
use SpaceXStats\Models\Part;
use SpaceXStats\Models\PartFlight;
use SpaceXStats\Models\Payload;
use SpaceXStats\Models\PrelaunchEvent;
use SpaceXStats\Models\Spacecraft;
use SpaceXStats\Models\SpacecraftFlight;
use SpaceXStats\Models\Telemetry;
use SpaceXStats\Library\Enums\LaunchSpecificity;

class MissionManager {
    private $input, $errors = [];
    private $mission;

    public function __construct(Mission $mission, Payload $payload, PartFlight $partFlight, Part $part, SpacecraftFlight $spacecraftFlight, Spacecraft $spacecraft, AstronautFlight $astronautFlight, Astronaut $astronaut, PrelaunchEvent $prelaunchEvent, Telemetry $telemetry) {
        $this->mission              = $mission;
        $this->payload              = $payload;
        $this->partFlight           = $partFlight;
        $this->part                 = $part;
        $this->spacecraftFlight     = $spacecraftFlight;
        $this->spacecraft           = $spacecraft;
        $this->astronautFlight      = $astronautFlight;
        $this->astronaut            = $astronaut;
        $this->telemetry            = $telemetry;
        $this->prelaunchEvent       = $prelaunchEvent;
    }

    public function isValid() {
        // Get the input
        $this->input = Input::all();

        // Validate the mission model
        $missionValidity = $this->mission->isValid($this->input('mission'));
        if ($missionValidity !== true) {
            $this->errors[] = $missionValidity;
        }

        // Validate any payload models
        if (array_key_exists('payloads', $this->input['mission'])) {
            $payloads = $this->input('payloads');

            foreach ($payloads as $payload) {
                $payloadValidity = $this->payload->isValid($payload);
                if ($payloadValidity !== true) {
                    $this->errors['payloads'][] = $payloadValidity;
                }
            }
        }

        // Validate any partFlight models
		/*
        $partFlights = $this->input['mission']['part_flights'];
        foreach ($partFlights as $partFlight) {

            $partFlightValidity = $this->partFlight->isValid($partFlight);
            if ($partFlightValidity !== true) {
                $this->errors['part_flights'][] = $partFlightValidity;
            }

            // Validate the part model
            $partValidity = $this->part->isValid($partFlight['part']);
            if ($partValidity !== true) {
                $this->errors['parts'][] = $partValidity;
            }
        }

        // Validate the spacecraftFlight model
        if (!is_null($this->input['mission']['spacecraft_flight'])) {
            $spacecraftFlight = $this->input['mission']['spacecraft_flight'];

            $spacecraftFlightValidity = $this->spacecraftFlight->isValid($spacecraftFlight);
            if ($spacecraftFlightValidity !== true) {
                $this->errors['spacecraft_flight'][] = $spacecraftFlightValidity;
            }

            // Validate the spacecraft model
            $spacecraftValidity = $this->spacecraft->isValid($spacecraftFlight['spacecraft']);
            if ($spacecraftValidity !== true) {
                $this->errors['spacecraft'][] = $spacecraftValidity;
            }

            // Validate any astronaut flights model
            if (array_key_exists('astronaut_flights', $spacecraftFlight)) {
                foreach ($spacecraftFlight['astronaut_flights'] as $astronautFlight)
                {
                    $astronautFlightValidity = $this->astronautFlight->isValid($astronautFlight);
                    if ($astronautFlightValidity !== true) {
                        $this->errors['astronaut_flights'][] = $astronautFlightValidity;
                    }

                    // Validate the astronaut model
                    $astronautValidity = $this->astronaut->isValid($astronautFlight['astronaut']);
                    if ($astronautValidity !== true) {
                        $this->errors['astronauts'][] = $astronautValidity;
                    }
                }
            }
        }

        // Validate any prelaunch event models

        // Validate any telemetry models
        if (array_key_exists('telemetry', $this->input['mission'])) {
            foreach ($this->input['mission']['telemetry'] as $telemetry) {
                $telemetryValidity = $this->telemetry->isValid($telemetry);
                if ($telemetryValidity !== true) {
                    $this->errors['telemetry'][] = $telemetryValidity;
                }
            }
        }*/

        return empty($this->errors);
    }

    public function create() {
        // Create the mission
        DB::beginTransaction();
        try {

            // Fill mission
            $this->mission->fill($this->input('mission'));
            $this->mission->status = 'Upcoming';
            $this->mission->push();

            //$this->managePayloadRelations();
            //$this->managePartFlightRelations();
            //$this->manageSpacecraftFlightRelation();
            //$this->createPrelaunchEventRelation();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return $this->mission;
    }

    public function update() {

        $this->mission = Mission::with('payloads', 'partFlights.part', 'spacecraftFlight.astronautFlights.astronaut', 'telemetry')->find($this->input('mission')['mission_id']);

        DB::beginTransaction();
        try {
            // Fill mission
            $this->mission->fill($this->input('mission'));
			
			
            $this->mission->save();
            $this->mission->push();

            // Update any relations, create new relations, delete any relations which have been removed.
            //$this->managePayloadRelations();
            //$this->managePartFlightRelations();
            //$this->manageSpacecraftFlightRelation();
            //$this->manageTelemetryRelations();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return $this->mission;
    }

    private function input($filter) {
        if ($filter == 'mission') {
            $mission = $this->input['mission'];
			
			if($mission['launch_specificity'] >= LaunchSpecificity::Day){
				$mission['launch_exact'] = $mission['launch_date_time'];
			}
			else{
				$mission['launch_approximate'] = $mission['launch_date_time'];
			}
            unset($mission['payloads'], $mission['part_flights'], $mission['spacecraft_flight'], $mission['prelaunch_events'], $mission['telemetry']);
            return $mission;

        } else if ($filter == 'payloads') {
            return $this->input['mission']['payloads'];

        } else if ($filter == 'spacecraft') {
            return $this->input['mission']['spacecraft_flight']['spacecraft'];
        }
    }

    private function createPayload($input) {
        $payload = new Payload($input);
        $payload->mission()->associate($this->mission);
        $payload->save();
    }

    private function managePayloadRelations() {
        $currentPayloads = $this->mission->payloads->keyBy('payload_id');

        foreach ($this->input('payloads') as $payloadInput) {

            // If the payload exists, update it, otherwise, create it
            if (array_key_exists('payload_id', $payloadInput)) {
                $payload = $currentPayloads->pull($payloadInput['payload_id']);
                $payload->fill($payloadInput);
                $payload->save();

            } else {
                $this->createPayload($payloadInput);
            }
        }

        // Delete any remaining payloads
        if (!$currentPayloads->isEmpty()) {
            Payload::whereIn('payload_id', $currentPayloads->keys())->delete();
        }
    }

    private function managePartFlightRelations() {
        $currentPartFlights = $this->mission->partFlights->keyBy('part_flight_id');

        foreach ($this->input['mission']['part_flights'] as $partFlightInput) {

            // If the partFlight exists, update it, otherwise, create it
            if (array_key_exists('part_flight_id', $partFlightInput)) {
                $partFlight = $currentPartFlights->pull($partFlightInput['part_flight_id']);
            } else {
                $partFlight = new PartFlight();
            }

            // Create part if it is not being reused or otherwise find it
            $partInput = array_pull($partFlightInput, 'part');
            $part = array_key_exists('part_id', $partInput) ? Part::find($partInput['part_id']) : new Part();
            $part->fill($partInput);
            $part->save();

            $partFlight->part()->associate($part);
            $partFlight->mission()->associate($this->mission);
            $partFlight->save();
        }

        // Delete any remaining partflights
        if (!$currentPartFlights->isEmpty()) {
            $partFlightsToDelete = PartFlight::whereIn('part_flight_id', $currentPartFlights->keys());

            // For each partFlight, if it has a part which has not been used elsewhere, also delete it
            foreach($partFlightsToDelete as $partFlightToDelete) {

                if ($partFlight->part()->partFlights()->count() > 1) {
                    $partFlightToDelete->part()->delete();
                }

                $partFlightToDelete->delete();
            }
        }
    }
    private function manageSpacecraftFlightRelation() {
        $currentSpacecraftFlight = $this->mission->spacecraftFlight;
        $spacecraftInput = array_pull($this->input['mission']['spacecraft_flight'], 'spacecraft');

        // If the spacecraft flight input exists (create/update)
        if (!is_null($this->input['mission']['spacecraft_flight'])) {
            $spacecraftFlight = $currentSpacecraftFlight->count() == true ? $this->mission->spacecraftFlight->fill($spacecraftInput) : new SpacecraftFlight($spacecraftInput);

            // Create spacecraft if it is not being reused or otherwise find it and update it
            $spacecraft = array_key_exists('spacecraft_id', $spacecraftInput) ? Spacecraft::find($spacecraftInput['spacecraft_id']) : new Spacecraft();
            $spacecraft->fill($spacecraftInput);

            $spacecraft->spacecraftFlights()->save($spacecraftFlight);
            $spacecraftFlight->mission()->associate($this->mission);

            // Now manage astronautFlights and astronauts
            $this->manageAstronautFlightsRelation();


        // If the input spacecraft flight does not exist and the current spacecraft flight does (delete)
        } elseif (is_null($this->input['mission']['spacecraft_flight']) && !is_null($currentSpacecraftFlight)) {
            $this->mission->spacecraftFlight->delete();

            // Also delete any astronaut flights & spacecraft (Maybe manage with database cascading?)
        }
    }

    private function manageAstronautFlightsRelation() {
        $astronautFlightsInput = array_pull($this->input['mission']['spacecraft_flight'], 'astronaut_flights');

            foreach ($astronautFlightsInput as $astronautFlightInput)
            {
                $astronautFlight = array_key_exists('astronaut_flight_id', $astronautFlightInput) ? AstronautFlight::find($astronautFlightInput['astronaut_flight_id']) : new AstronautFlight();

                $astronautId = $astronautFlightInput['astronaut']['astronaut_id'];
                $astronaut = is_null($astronautId) ? new Astronaut() : Astronaut::find($astronautId);

                $astronaut->fill($astronautFlightInput['astronaut']);

                $astronaut->astronautFlights()->save($astronautFlightInput);
                $astronautFlight->spacecraftFlight()->associate($spacecraftFlight);
            }
    }

    private function createPrelaunchEventRelation() {
        $prelaunchEvent = PrelaunchEvent::create([
            'event' => 'Announcement',
            'occurred_at' => Carbon::now(),
            'summary' => 'Mission created'
        ]);
        $prelaunchEvent->mission()->associate($this->mission);
        $prelaunchEvent->save();
    }

    private function manageTelemetryRelations() {
        $currentTelemetry = $this->mission->telemetry->keyBy('telemetry_id');

        foreach ($this->input['mission']['telemetry'] as $telemetryInput) {

            // If the telemetry exists, update it, otherwise, create it
            if (array_key_exists('telemetry_id', $telemetryInput)) {
                $telemetry = $currentTelemetry->pull($telemetryInput['telemetry_id']);
                $telemetry->fill($telemetryInput);
                $telemetry->save();

            } else {
                $telemetry = new Telemetry($telemetryInput);
                $telemetry->mission()->associate($this->mission);
                $telemetry->save();
            }
        }

        // Delete any telemetry payloads
        if (!$currentTelemetry->isEmpty()) {
            Telemetry::whereIn('telemetry_id', $currentTelemetry->keys())->delete();
        }
    }

    public function getErrors() {
        return $this->errors;
    }
}