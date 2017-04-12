@extends('templates.main')
@section('title', 'Order Launches')

@section('content')
<body class="future-launches" ng-controller="orderMissionsListController" ng-strict-di>


    @include('templates.header')

    <div class="content-wrapper">
        <h1>Set Launch Order</h1>
        <main>
			<input type="submit" value="Save Launch Order" ng-click="saveChanges()" />
            <section>
				<div ng-repeat="mission in missions | orderBy:'launch_order_id'">
					
					<div class="gr-12">@{{mission.name}}</div>
					<div class="gr-3"><input type="number" ng-model="mission.launch_order_id"/></div>
					<div class="gr-3">@{{mission.launch_date_time}}</div>
					<div class="gr-12"><button style="line-height: 12px; font-size: 12px;" ng-click="incrementRemaining(mission.launch_order_id)" >Incremement Remaining</button></div>
				</div>
            </section>
        </main>
    </div>
	<script type="text/javascript">
        (function() {
            var app = angular.module("app");
            app.constant("CSRF_TOKEN", '{{ csrf_token() }}');
        })();
    </script>
</body>
@stop