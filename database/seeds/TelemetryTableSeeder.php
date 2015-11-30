<?php
use Illuminate\Database\Seeder;
use SpaceXStats\Models\Telemetry;

class TelemetryTableSeeder extends Seeder {
    public function run() {
        $this->Falcon1Flight1();
        $this->Falcon1Flight2();
        $this->Falcon1Flight3();
        $this->COTS2Plus();
    }

    public function Falcon1Flight1() {
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 0,
            'readout'       => '0'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 1,
            'readout'       => 'Plus One'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 2,
            'readout'       => 'Plus Two'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 3,
            'readout'       => 'Plus Three'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 4,
            'readout'       => 'Plus Four'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 5,
            'readout'       => 'Plus Five'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 6,
            'readout'       => 'Plus Six'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 7,
            'readout'       => 'Plus Seven'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 8,
            'readout'       => 'Plus Eight'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 9,
            'readout'       => 'Plus Nine'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 10,
            'readout'       => 'Plus Ten'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 13,
            'readout'       => 'We have liftoff confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 16,
            'readout'       => 'Copy'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 18,
            'readout'       => 'Good signal strength'
        ]);
        Telemetry::create([
            'mission_id'    => 1,
            'timestamp'     => 25,
            'readout'       => 'This is the LC on the countdown net, Falcon 1 is airborne at this time.'
        ]);
    }

    public function Falcon1Flight2() {
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 5,
            'readout'       => 'We have liftoff'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 6,
            'readout'       => 'And we have liftoff'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 11,
            'readout'       => 'Falcon has cleared the tower'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 19,
            'readout'       => 'Pitchover'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 20,
            'readout'       => 'First stage engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 40,
            'readout'       => 'Velocity 128 metres per second, altitude 2.6 kilometres.',
            'velocity'      => 128,
            'altitude'      => 2600
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 46,
            'readout'       => 'Guidance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 49,
            'readout'       => 'Kwajalein RF telemetry lock nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 59,
            'readout'       => 'First stage engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 62,
            'readout'       => 'Nominal velocity'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 76,
            'readout'       => 'Max-Q'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 79,
            'readout'       => "Vehicle's passing through Max-Q"
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 84,
            'readout'       => 'Velocity 450 metres per second, altitide 13.9 kilometres',
            'velocity'      => 450,
            'altitude'      => 13900
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 100,
            'readout'       => 'Kwajalein RF telemetry lock nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 110,
            'readout'       => 'First stage engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 125,
            'readout'       => 'Guidance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 129,
            'readout'       => 'Telemetry lock nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 149,
            'readout'       => 'Merlin engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 165,
            'readout'       => 'Coming up on stage separation'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 172,
            'readout'       => '[inaudible] [cheering] Stages are separated!'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 177,
            'readout'       => 'Second stage ignition confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 189,
            'readout'       => 'Second stage engine ignition nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 191,
            'readout'       => 'Coming up on fairing separation'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 198,
            'readout'       => 'Fairing separaton is confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 201,
            'readout'       => 'copy'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 202,
            'readout'       => 'Fairing sep confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 206,
            'readout'       => 'Vehicle velocity is 2634 metres per second, altitude 117 kilometres',
            'velocity'      => 2634,
            'altitude'     => 117000
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 212,
            'readout'       => 'Second stage engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 236,
            'readout'       => 'Second stage engine performance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 241,
            'readout'       => 'yeah, guidance nominal too'
        ]);
        Telemetry::create([
            'mission_id'    => 2,
            'timestamp'     => 252,
            'readout'       => 'yep, velocity 2778 metres per second, altitude 161 kilometres down',
            'velocity'      => 2778,
            'altitude'      => 161000
        ]);
    }

    public function Falcon1Flight3() {
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 1,
            'readout'       => 'We have liftoff'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 2,
            'readout'       => 'We have liftoff of the SpaceX Falcon 1 launch vehicle. Falcon has cleared the tower.'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 15,
            'readout'       => 'Plus Fifteen seconds'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 16,
            'readout'       => 'Pitchover'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 21,
            'readout'       => 'Plus Twenty seconds'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 24,
            'readout'       => 'Velocity is 95 metres per second. Altitude is 1.4 kilometres.',
            'altitude'       => 1400,
            'velocity'      => 95
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 32,
            'readout'       => 'T plus thirty seconds'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 41,
            'readout'       => 'This is the AVI, vehicle systems are nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 44,
            'readout'       => 'First stage propulsion is nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 46,
            'readout'       => 'Guidance nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 50,
            'readout'       => 'Approaching Mach 1'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 54,
            'readout'       => 'Mach 1'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 62,
            'readout'       => 'Falcon 1 is supersonic at this time.'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 68,
            'readout'       => 'Approaching Max-Q'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 70,
            'readout'       => 'Max-Q'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 73,
            'readout'       => '[inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 80,
            'readout'       => 'T plus 1 minute and 20 seconds. Relative velocity 5- 600 metres per second, altitude 18.5 kilometres',
            'velocity'      => 600,
            'altitude'      => 18500
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 96,
            'readout'       => '[inaudible] safed'
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 100,
            'readout'       => 'T plus 1 minute 40 seconds. The vehicle is headed downrange a velocity of 1050 metres per second and an altitude of 35 kilometres',
            'velocity'      => 1050,
            'altitude'      => 35000
        ]);
        Telemetry::create([
            'mission_id'    => 3,
            'timestamp'     => 146,
            'readout'       => "Uhh. We are hearing from the launch control center that there has been an anomaly on the vehicle, we don't have any information about what that anomaly is at this time. Uhh, we will of course be doing an assessment situation and providing information as soon as it becomes available. Uhh, check back to the website www.spacex.com for the latest information"
        ]);
    }

    public function Falcon1Flight4() {
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're in stage 1"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'We have liftoff indication'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'We have liftoff'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'SpaceX Falcon 1 launch vehicle, Falcon has cleared the tower'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Plus twelve'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Pitchkick'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Plus twenty seconds'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => '[inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Vehicle now [inaudible] nominal, gravity turn'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Power systems nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'T plus sixty seconds'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Got Max-Q'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'First stage propulsion performance is nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Vehicle has a velocity of 630 metres per second and an altitude of 19 kilometres'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'You can see the plume of the vehicle- the plume of the engine expanding as we get into more rareified atmosphere'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "It also gets blacker as there's less and less oxygen to support post-combustion"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Vehicle has a current velocity of 1000 metres per second and an altitude of 32 kilometres'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'T plus two minutes'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Vehicle is down- has an altitude of 50 kilometres, a velocity of 1700 metres per second'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'First stage performance still nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'We are approaching Main Engine Cutoff'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "There'll be a five second delay before stage separation"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => '[inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Second stage tank stable'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'MECO'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Stage separation confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'And Kestrel ignition'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Perfect'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Second stage [inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => '[inaudible] manouver'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'You can hear the cheers in the background'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'There goes the stiffening bands'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => '[inadible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'You can see the limb of the Earth in the upper left side of the screen'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'T plus three minutes we have a relative velocity of 2770 metres per second and an altitude of 130 kilometres'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'The Kestrel engine will burn for more than six minutes in the ride to orbit, here we see the fairing separation'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'There goes the fairing'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Fairing separation confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Second stage propulsion performance is nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at three minutes and thirty seconds into the flight, we have a relative velocity of 2800 metres per second and an altitude of 170 kilometres"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Second stage guidance is nominal'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'T plus four minutes'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at T plus four minutes. We have a relative velocity of approximately 3000 metres per second and an altitude of 200 kilometres"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "You can see the Kestrel nozzle glowing a dull red. It's actually designed to glow almost white-hot if necessary"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Very steady attitude we're seeing"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at T plus five minutes. We have a relative velocity of approximately 3200 metres per second and an altitude of 253 kilometres. All systems are nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "About four minutes remaining in the second stage burn."
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at T plus six minutes. Vehicle velocity is approximately 3600 metres per second and an altitude of 290 kilometres"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "The vast majority of the acceleration occurs during this latter half of the second stage burn, as the mass of the vehicle- the propellant load, decreases"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at T plus seven minutes we have a relative velocity of 4200 metres per second, and an altitude of 315 kilometres. We're beginning to lose stage 1 telemetry"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Second stage propulsion is performing nominally"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Guidance is performing nominally"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're at T plus eight minutes with a relative velocity of 5200 metres per second and an altitude of 328 kilometres"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We are getting very close to orbital velocity"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We appear to have loss of signal. This is not necessarily a bad thing, we were expecting to lose signal sometime around here. It can be highly variable depending on the weather conditions at the time. So, we of course want to see what happens over the next sixty seconds. We were about sixty seconds away from a nominal shutdown. We will be getting back to you as soon as we have more information."
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "-telemetry and video."
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "9 minutes 30 seconds"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Second stage approaching SECO"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "And that would be a nominal SECO!"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "SECO confirmed!"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Which means Falcon 1 has made history as the first privately developed launch vehicle to reach orbit from the ground"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Payload separation confirmed"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'That was...'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Repeating: simulated payload deploy confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'T plus ten minutes'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "You're seeing a forward shot, that is where we would nomrally see a payload separation, in this case we are not separating..."
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'FTS [inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Hey congratulations, this is fantastic"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Thank you"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We're still showing active on second stage telemetry"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "We are heading over the horizon with respect to the launch range, so we are expecting loss of signal any second now, you can see it starting to break up"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "But this is a very good day at SpaceX"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "Max I think it's important when we lose our signal here to put this in perspective, what SpaceX has been able to achieve today"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "LC we have a [inaudible] on safety radar, nominal [inaudible]"
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => '[inaudible]'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => "SpaceX has designed and developed this vehicle from the ground up from a blank sheet of paper they've done all the design and all the testing in house. We don't outsource, and we have achieved this with a company that is only now 500 people, and it has all occurred in under 6 years. And this is just the groundbreaker; this is just the vanguard; of the much larger Falcon 9 launch vehicle debuting from Cape Canaveral next year, and the Dragon spacecraft that will be debuting in June o next year and will be providing cargo services to the International Space Station. So we have big plans, even beyond that, here at SpaceX. Including human space transportation in the Dragon launch vehicle and on the Falcon 9- uhh, Falcon 9 launch vehicle and the Dragon spacecraft, and this is really just the beginning, this is just the tip of the iceberg here. There's a lot of people who have worked very hard for a very long time., tremendous commitment from them and their families to get to this point. It's really an incredible achievement."
        ]);
        /* STILL MORE TELEMETRY
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Fairing separation confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Fairing separation confirmed'
        ]);
        Telemetry::create([
            'mission_id'    => 4,
            'timestamp'     => 1,
            'readout'       => 'Fairing separation confirmed'
        ]);*/

    }

    public function Falcon1Flight5() {
        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 0,
            'readout'       => 'T-0'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 1,
            'readout'       => 'Plus One'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 2,
            'readout'       => 'Plus Two'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 3,
            'readout'       => 'Plus Three'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 4,
            'readout'       => 'We have liftoff of the SpaceX Falcon 1 carrying RazakSAT satellite for ATSB'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 10,
            'readout'       => 'Falcon 1 has cleared the tower'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 16,
            'readout'       => 'Vehicle has begun moving downrange'
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle's currently travelling 27 metres per second at point 3 kilometres above the pad"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Power systems nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "First stage propulsion is nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Approaching Mach 1"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle's in the transonic region about to go supersonic"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T plus 1 minute"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "This is Max-Q, maximum dynamic pressure. This is the period of greatest stress on the vehicle structure"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "First stage propulsion performing nominally"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle currently has a velocity of 500 m/s at an altitude of 15 km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Passed Max-Q"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "You can see the plume getting darker..."
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T plus 1 minute 40 seconds, velocity 830m/s at an altitude of 30km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "...and blacker, as we get to higher rareified atmosphere"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "First stage propulsion still performing nominally"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle's currently travelling 1300m/s at an altitude of 38km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Inertial guidance"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle's trimming out alpha in preparation for MECO. There'll be a 5 second delay between main engine cutoff, and stage separation, then 4 more seconds before second stage ignition. There's MECO!"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "First stage MECO"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Stage separation, and Kestrel ingition"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Stage separation confirmed"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage ignition confirmed"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Okay, the stages have separation, the second stage has ignited. The second stage is currently travelling 2800m/s at an altitude of 108km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Approaching fairing separation"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Coming up to fairing sep"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Camera move to forward view"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Fairing separation confirmed"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "We've had a clean fairing sep, both halves looked equal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage propulsion performance is nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Fairing came off beautifully"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "You'll see the stiffening bands fall of the Kestrel Nozzle in a minute [inaudible]"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+3minutes 40 seconds, the vehicle's travelling 3000m/s at an altitude of 150km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Good telemetry lock on both stages"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "The Kestrel engine fires for over 6 minutes on the way to orbit, the vast majority of acceleration occurs in this phase."
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Passing throught T+4 minutes, the vehicle's travelling 3000m/s at an altitude of 172km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Power systems nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "I should point out that we may lose video and telemetry, just prior to second stage shutdown, depending on the vagaries of upper atmosphere RF propogation"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+4m30s, both velocity and altitude are nominal, at 3200m/s and an altitude of 196km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "But that's another 4 and a half minutes from now"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Telemetry lock still good"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage propulsion performing nominally"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "We are still seeing nominal flight"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+5m30s, current velocity 3600m/s at an altitude of 230km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "At 230km altitude, Falcon 1 is currently well beyond the official boundary of space at 100km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Currently three minutes into the second stage burn, passing through T+6 minutes. Velocity 3800m/s at an altitude at 243km, both are above nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Propulsion is performing nominally on the second stage"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+6 minutes 30 seconds, velocity 4150m/s, altitude 255km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "We are about 2 and a half minutes from SECO"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage propulsion's performing nominally"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+7 minutes, current velocity 4400m/s at an altitude of 260km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Power systems still nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance is nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "At SECO, we will be in a parking orbit. 38 minutes later over Ascension Island in the South Pacific we will relight the second stage engine to circularize the orbit - and after that short second burn we will deploying the RazakSAT satellite into orbit"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+7minutes 30 seconds, velocity 4800m/s, altitude 265km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage telemetry still nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Second stage propulsion nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Guidance is nominal"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Launchpad safing is complete. Passing through T+8 minutes, velocity 5400m/s, at an altitude of 267km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "As I mentioned, we are expectin to lose RF signal, telemetry, and video sometime in the next minute and a half, and it may be before second stage engine shutdown"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Somewhere here we're expecting some dropouts"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "SECO is about 9 minutes 15 seconds"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Vehicle's in terminal guidance"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Propulsion performing nominally"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+9 minutes. Vehicle's travelling 6400m/s, at an altitude of 266km"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Seeing some dropouts in the telemetry and video"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "T+9m 30s, okay we've currently gone over the horizon, we are experiencing some losses in telemetry, but vehicle has completed shutdown"
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "That's SECO. And with SECO, that places Falcon 1 Second Stage and RazakSAT satellite into the parking orbit."
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "Excellent, well after that beautiful launch, we are looking at a loss of second stage signal, because as you have heard, it has passed over the horizon of the launch site. Everything looks to be going well, and this will end our live webcast of the RazakSAT mission today. Please continue to monitor the progress of this mission on our website spacex.com. You can also download photos and videos of todays launch in the coming days."
        ]);

        Telemetry::create([
            'mission_id'    => 5,
            'timestamp'     => 21,
            'readout'       => "[inaudible] power nominal"
        ]);
    }

    public function COTS2Plus() {
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 1,
            'readout'       => "Stage 1"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 3,
            'readout'       => "We have liftoff of the Falcon 9"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 8,
            'readout'       => "Falcon 9 has cleared the tower"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 10,
            'readout'       => "Starting pitchkick"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 24,
            'readout'       => "Starting gravity turn"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 28,
            'readout'       => "First stage engines at full power, looking good"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 39,
            'readout'       => "We have a solid telemetry link and power systems are nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 51,
            'readout'       => "First stage propellant utilization is active"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 57,
            'readout'       => "Vehicle is on a nominal trajectory. Altitude 5.3 kilometres, velocity 225 metres per second, and downrange distance of 6 tenths of a kilometre.",
            'altitude'      => 5300,
            'velocity'      => 225,
            'downrange'     => 600
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 71,
            'readout'       => "Vehicle is supersonic"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 83,
            'readout'       => "Vehicle has reached maximum aerodynamic pressure"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 90,
            'readout'       => "Propulsion's performing nominally, starting stage two engine chill"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 99,
            'readout'       => "We have a solid RF link and power systems are nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 118,
            'readout'       => "Vehicle's on a nominal trajectory, 30 kilometres altitude, 1 kilometre per second velocity, and downrange distance 20 kilometres",
            'altitude'      => 30000,
            'velocity'      => 1000,
            'downrange'     => 20000
        ]);

        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 129,
            'readout'       => "Dragon power systems are nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 147,
            'readout'       => "Vehicle's on a nominal trajectory, 53 kilometres altitude, 1.7 kilometres per second velocity, and a downrange distance of 51 kilometres",
            'altitude'      => 53000,
            'velocity'      => 1700,
            'downrange'     => 51000
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 160,
            'readout'       => "Approaching MECO 1"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 170,
            'readout'       => "MECO 1. Planned shutdown on engines 1 and 9."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 175,
            'readout'       => "First stage impact point past min-MECO"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 181,
            'readout'       => "MECO 2"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Nominal velocity at MECO"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 188,
            'readout'       => "Stage sep confirmed"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 195,
            'readout'       => "MVac ignition confirmed"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 235,
            'readout'       => "The Dragon uh- The Dragon nosecone has been jettisoned"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 240,
            'readout'       => "Stage 2 propulsion systems nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 257,
            'readout'       => "Vehicle remains on a nominal trajectory, 176 kilometres altitude, velocity of 3 kilometres per second, downrange distance 320 kilometres",
            'altitude'      => 176000,
            'velocity'      => 3000,
            'downrange'     => 320000
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 267,
            'readout'       => "And power systems are nominal and we still have a solid telemetry link"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 271,
            'readout'       => "OSM this is LC, please move to net A."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 273,
            'readout'       => "Stage 2 propellant utilization is active"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 317,
            'readout'       => "Vehicle remains on a nominal trajectory. 220 kilometres altitude, 3.4 kilometres per second, and downrange distance of 470 kilometre",
            'altitude'      => 220000,
            'velocity'      => 3400,
            'downrange'     => 470000
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 343,
            'readout'       => "Second stage propulsion performing as expected"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 364,
            'readout'       => "Second stage power systems looking good and we have a solid telemetry link"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 377,
            'readout'       => "Vehicle remains on a nominal trajectory. 269 kilometres in altitude, velocity of 4 kilometres per second, and a downrange distance of 712 kilometres",
            'altitude'      => 269000,
            'velocity'      => 4000,
            'downrange'     => 712000
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 418,
            'readout'       => "All stations MD, step uh- procedure 7 dot 1 0 0 complete, we're on uh- procedure 7 dot 1 0 1."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 430,
            'readout'       => "MVac and stage 2 performance is good"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 448,
            'readout'       => "Vehicle remains on a nominal trajectory. 300 kilometres in altitude, velocity of 5 kilometres per second, and a downrange distance of 1000 kilometres; IMU sensor remains healthly, and GPS lock is verified",
            'altitude'      => 300000,
            'velocity'      => 5000,
            'downrange'     => 1000000
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 465,
            'readout'       => "And we are picking up data from New Hampshire"
        ]);

        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 518,
            'readout'       => "Vehicle's in terminal guidance"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 526,
            'readout'       => "Vehicle has passed the European gate"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 546,
            'readout'       => "FTS is safed"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 555,
            'readout'       => "Roughly 30 seconds till Dragon sep"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 565,
            'readout'       => "MVac shutdown confirmed"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 568,
            'readout'       => "Dragon's in separation"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 569,
            'readout'       => "Falcon 9 and Dragon are in orbit"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 570,
            'readout'       => "Dragon's in separation state"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 571,
            'readout'       => "[inaudible] prime"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 573,
            'readout'       => "[inaudible]"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 578,
            'readout'       => "All stations continue procedures on 7 1 dot-"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 580,
            'readout'       => "Apogee 346 kilometres, perigee 297 kilometre"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 594,
            'readout'       => "Inclination 51.66 degrees"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 598,
            'readout'       => "Cameras forward"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 602,
            'readout'       => "Dragon sep"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 610,
            'readout'       => "Start of payload settling deploy"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 629,
            'readout'       => "Dragon is now freeflying in orbit around the Earth, we are very excited, if we maintain video coverage we hope to see deployment of the solar arrays, if we loose video, everything is likely still operating nominally, we're just at the limit of our signal. We have about a minute before the fairings that house the solar arrays are going to jettison, and that is going to automatically trigger their deployment. Right now the Dragon's propellant system's priming itself, and the thrusters are going to fire, and then it will- uh oh. Hopefully we can hold signal here. Boy, we have just about 40 seconds to wait for this. Let's see if we can't hold our signal and watch these solar arrays deploy."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 678,
            'readout'       => "Solar array deploy attitude"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 680,
            'readout'       => "Confirm Draco thruster firings"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 687,
            'readout'       => "Attitude looks good"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 696,
            'readout'       => "Dragon is in array deploy"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 698,
            'readout'       => "Props is nominal"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 700,
            'readout'       => "Dragon solar array deployment confirmed"
        ]);

        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 702,
            'readout'       => "New Foundland AOS"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 703,
            'readout'       => "Solar array deployment!"
        ]);
        /*Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Solar arrays have deployed"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Well, you can see the solar arrays deploying, this is a great moment. Of course, this is just the first step of a very complex mission. Uhh... but from all accounts we have Dragon orbiting the Earth with the solar arrays deployed!"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Power global"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "This is so good!"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "We have a couple days of really difficult challenges before we get to the space station, but, but both solar arrays are deployed. Dragon is performing nominally and we are looking forward to a great mission here to the International Space Station. Hopefully become the first private company to service our international community at the space station"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Power global"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Go ahead and acknowledge MD"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Okay, power global ackn-"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "LD, MDI on countdown"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "LD's on a phone call right now MD"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Yeah, I copy that LC. Uhh, we're gonna' be switching off countdown net, thanks for the ride."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "K, all stations. This is MD on Mission A. We're on 1 dot 1 0."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Dragon is in coelliptic plan, no comm"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "Well as expected. Dragon is just about at the limit of the New Foundland groundstation, we're probably going to lose video shortly, but right now, Dragon is stil communicating with mission control here in Hawthorne, California, and everything looks great, it continues to circle the globe. We can hear the audience here. Everyone at SpaceX, we have 1800 plus people, we're all working really hard, and we're on our way to a great mission. We still have 3 and a half days, a lot of test manovuers before we get to the station, so stay in touch with us at spacex.com and Twitter, and continue to cheer us on."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "So, uhh, let's see if I can talk now. We had a great launch today. This is the third successful flight of Falcon 9; the second time we've put Dragon safely into orbit, this is so awesome. We definitely hope to continue to the success over the next two weeks, where we are making progress to the space station; and I feel pretty good that we are going to be the first private company to ever visit the International Space Station, this is so exciting. Please be sure to stay tuned to spacex.com, and Facebook, and Twitter, and all those things, because there's all kinds of great tweets right now. Yeah, please just stay up to date with what's going on."
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "And we want to extend a special thanks to NASA for their teamwork and support, and todays mission, and getting here, to all of our customers and supporters over the years, to the worldwide network of trackign stations that are going to be helping us with Dragon going to the space station and back over the coming couple of weeks, and finally to the Air Force and the folks at Cape Canveral for the great support in getting todays launch off the pad!"
        ]);
        Telemetry::create([
            'mission_id'    => 8,
            'timestamp'     => 183,
            'readout'       => "So, on behalf of the 1800 people here at SpaceX, we thank you so much for watching this amazing mission today. It's a great day, it's almost surreal, so cool. Yeah, and just please continue to watch as Dragon makes its progress to the space station. Thank you."
        ]);*/
    }
}