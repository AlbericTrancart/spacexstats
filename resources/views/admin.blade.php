@extends('templates.main')
@section('title', 'Admin')

@section('content')

<body class="admin">

    @include('templates.header')

    <div class="content-wrapper">
        <h1>Admin</h1>
        <main>
            <nav class="sticky-bar">
                <ul class="container">
                    <li class="gr-1"><b>Admin Tasks</b></li>
                </ul>
            </nav>
            <section>
                <ul>
                    <li><a href="/missions/create">Create A Mission</a></li>
					<li><a href="/admin/clearcache">Clear Laravel Cache</a></li>
                </ul>
            </section>
        </main>
    </div>
</body>
@stop