<x-web-layout>
    <h1 class="text-xl md:text-4xl pb-4 font-bold">{{ $info->title }}</h1>
    <div class="text-gray-700 leading-normal">
        <p>{{ $info->description }}</p>
    </div>

    <div class="bg-gray-10 p-5">{!! $info->content !!}</div>
</x-web-layout>