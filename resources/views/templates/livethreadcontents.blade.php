# Welcome to the {{ \Redis::hget('live:reddit', 'title') }}!

{{ \Redis::hget('live:description', 'raw') }}

### Watching the launch live

To watch the launch live, pick your preferred streaming provider from the table below:

| [SpaceX Stats Live (Webcast + Live Updates)](https://spacexstats.com/live) |
| --- |
| [SpaceX Livestream (Webcast)](https://spacex.com/webcast) |
@if (json_decode(\Redis::hget('live:streams', 'spacex'))->isAvailable)
| [SpaceX YouTube](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'spacex'))->youtubeVideoId }}) |
@endif
@if (json_decode(\Redis::hget('live:streams', 'spacexClean'))->isAvailable)
| [SpaceX (Clean) YouTube](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'spacexClean'))->youtubeVideoId }}) |
@endif
@if (json_decode(\Redis::hget('live:streams', 'nasa'))->isAvailable)
| [NASA TV Ustream](http://www.ustream.tv/nasahdtv) |
| [NASA TV YouTube](https://youtube.com/watch?v={{ json_decode(\Redis::hget('live:streams', 'nasa'))->youtubeVideoId }}) |
@endif

### Official Live Updates

| Time | Update |
|--- | --- |
@for($i = 0; $i <= 100; $i++)
@if (isset($updates[$i]))
| {{ $updates[$i]->timestamp }} | {{ $updates[$i]->update }} |
@endif
@endfor

@foreach(json_decode(\Redis::get('live:sections'), true) as $section)
### {{ $section['title'] }}

{{ $section['content'] }}
@endforeach

### Useful Resources, Data, ?, & FAQ
@foreach(json_decode(\Redis::get('live:resources'), true) as $resource)
@if ($resource['courtesy'] != null)
* [{{ $resource['title'] }}]({{ $resource['url'] }}), {{ $resource['courtesy'] }}
@else
* [{{ $resource['title'] }}]({{ $resource['url'] }})
@endif
@endforeach

### Participate in the discussion!

* First of all, Launch Threads are a party threads! We understand everyone is excited, so we relax the rules in these venues. The most important thing is that everyone enjoy themselves :D
* All other threads are fair game. We will remove low effort comments elsewhere!
* Real-time chat on our official Internet Relay Chat (IRC) [#spacex at irc.esper.net](https://kiwiirc.com/client/irc.esper.net/?nick=SpaceX_guest%7C?#SpaceX)
* Please post small launch updates, discussions, and questions here, rather than as a separate post. Thanks!

### Prevous /r/SpaceX Live Events

Check out previous /r/SpaceX Live events in the [Launch History page](http://www.reddit.com/r/spacex/wiki/launches) on our community Wiki.