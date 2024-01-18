@php
    /** @var array<\App\Models\Hand> $hands  */
@endphp

<div class="max-w-5xl mx-auto flex flex-col gap-20">
    @foreach($hands as $hand)
        <ul class="flex flex-wrap gap-4">
            @foreach($hand->cards as $card)
                <div class="border-2 border-black rounded-3xl w-36 h-48 flex flex-col justify-between p-2">
                    <div class="text-left">
                        {{ $card->rank->value }}
                    </div>

                    <div class="text-center">
                        {{ $card->suit->value }}
                    </div>

                    <div class="text-right">
                        {{ $card->rank->value }}
                    </div>
                </div>
            @endforeach
        </ul>
    @endforeach
</div>
