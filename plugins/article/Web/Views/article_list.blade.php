<x-web-layout>
    <div class="w-full md:pr-12 mb-12 divide-y">
        @foreach($data as $item)
            <article class="p-6 hover:bg-gray-50 rounded-lg">
                <h2 class="mb-4">
                    <a href="{{ plugin_url('detail',['id'=>$item->id]) }}" class="font-bold">{{ $item->title }}</a>
                </h2>
                <div class="text-gray-700 leading-normal">{{$item->description}}</div>
            </article>
        @endforeach
        {{$data}}
    </div>
</x-web-layout>