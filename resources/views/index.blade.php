<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Box chat SQL</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('img/favicon-16x16.png') }}">
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <script>
        var token = "{{ csrf_token() }}"
    </script>
</head>

<body class="antialiased">
    <div id="loading_page">
        <svg class="ip" viewBox="0 0 256 128" width="256px" height="128px" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#5ebd3e" />
                    <stop offset="33%" stop-color="#ffb900" />
                    <stop offset="67%" stop-color="#f78200" />
                    <stop offset="100%" stop-color="#e23838" />
                </linearGradient>
                <linearGradient id="grad2" x1="1" y1="0" x2="0" y2="0">
                    <stop offset="0%" stop-color="#e23838" />
                    <stop offset="33%" stop-color="#973999" />
                    <stop offset="67%" stop-color="#009cdf" />
                    <stop offset="100%" stop-color="#5ebd3e" />
                </linearGradient>
            </defs>
            <g fill="none" stroke-linecap="round" stroke-width="16">
                <g class="ip__track" stroke="#ddd">
                    <path d="M8,64s0-56,60-56,60,112,120,112,60-56,60-56" />
                    <path d="M248,64s0-56-60-56-60,112-120,112S8,64,8,64" />
                </g>
                <g stroke-dasharray="180 656">
                    <path class="ip__worm1" stroke="url(#grad1)" stroke-dashoffset="0"
                        d="M8,64s0-56,60-56,60,112,120,112,60-56,60-56" />
                    <path class="ip__worm2" stroke="url(#grad2)" stroke-dashoffset="358"
                        d="M248,64s0-56-60-56-60,112-120,112S8,64,8,64" />
                </g>
            </g>
        </svg>
    </div>
    <header id="header" class="text-center relative bg-dark-blue-800">
        <div class="container mx-auto p-3 sm:px-6 lg:px-8 fw-normal text-white">
            <a class="text-white text-decoration-none" href="" title="Box chat SQL">Box chat <b>SQL</b></a>
        </div>
    </header>
    <div id="main" class="pt-2 pb-5">
        <div class="p-4">
            <div class="body p-4 shadow-md border rounded-md overflow-hidden h-full">
                <form action="chat/upload" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="fileExcel" class="form-label"><b>Choose file</b></label>
                        <input class="form-control" type="file" id="fileExcel" name="filePost"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </div>
                    <div class="d-flex">
                        <button type="reset" class="btn btn-outline-danger btn-sm me-1">Reset</button>
                        <button id="choose-file" type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
                    </div>
                    {{-- {{ json_encode($data[0]['data']) }} --}}
                    @if (session('error'))
                        <div class="alert {{ session('status') ?? '' }} d-flex align-items-center pt-2 pb-2 mt-3"
                            role="alert">
                            <div>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif
                </form>
                @if (Arr::first($data))
                    <div id="box_load_data_table">
                        <div class="d-flex mt-3">
                            <button type="button" id="deleteAll" class="btn btn-outline-danger btn-sm me-1">Delete
                                All</button>
                            <button type="button" id="deleteSelected" class="btn btn-outline-danger btn-sm">Delete
                                selected</button>
                        </div>
                        <div id="load_data" class="table-responsive mt-2 tableFixHead bg-white">
                            @include('table', ['data' => Arr::first($data)])
                        </div>
                    </div>
                @endif
                <div id="boxChat" class="mt-5 rounded bg-light position-relative">
                    <div id="chatbox__messages" class="chatbox__messages pe-1"></div>
                    <div class="chatbox__inputPanel">
                        <div class="input-group align-items-center justify-content-between">
                            <button id="remove-all-history" onclick="handleRemoveCookie('chatSQL')"
                                title="Delete all history chat">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class=" iconify iconify--ri" width="1em"
                                    height="1em" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M17 6h5v2h-2v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V8H2V6h5V3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3Zm1 2H6v12h12V8Zm-9 3h2v6H9v-6Zm4 0h2v6h-2v-6ZM9 4v2h6V4H9Z">
                                    </path>
                                </svg>
                            </button>
                            <textarea oninput="checkValueMessages()" id="content-messages" class="form-control"
                                placeholder="Ask me anything...(Shift + Enter = line break)" aria-label="messages"
                                aria-describedby="send-messages"></textarea>
                            <button id="send-messages">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class=" iconify iconify--ri" width="1em"
                                    height="1em" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M1.946 9.316c-.522-.175-.526-.456.011-.635L21.043 2.32c.529-.176.832.12.684.638l-5.453 19.086c-.151.529-.456.547-.68.045L12 14l6-8l-8 6l-8.054-2.684Z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="load_request">
                        <div class="loader">
                            <div class="bar bar1"></div>
                            <div class="bar bar2"></div>
                            <div class="bar bar3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
