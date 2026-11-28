{{--
  Komponen Modal Konfirmasi Reusable
  Usage: <x-confirm-modal
            id="delete-order-{{ $order->id }}"
            title="Batalkan Pesanan?"
            message="Apakah Anda yakin ingin membatalkan pesanan #{{ $order->order_code }}? Tindakan ini tidak dapat dibatalkan."
            confirm-text="Ya, Batalkan"
            confirm-class="btn-danger"
            form-action="{{ route('orders.cancel', $order) }}"
            method="PATCH"
        />
--}}
@props([
    'id' => 'confirmModal',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin ingin melanjutkan?',
    'confirmText' => 'Ya, Lanjutkan',
    'confirmClass' => 'btn-primary',
    'cancelText' => 'Batal',
    'formAction' => '#',
    'method' => 'POST',
    'icon' => 'bi-exclamation-triangle-fill',
    'iconClass' => 'text-warning',
    'extraFields' => [],
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ $formAction }}" method="POST" class="p-0">
                @if($method !== 'POST')
                    @method($method)
                @endif
                @csrf

                @foreach($extraFields as $field)
                    {!! $field !!}
                @endforeach

                <div class="modal-header border-0 pb-0 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bi {{ $icon }} {{ $iconClass }} fs-4"></i>
                        {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-4 px-4">
                    <p class="text-secondary mb-0">
                            @if ($slot->isNotEmpty())
                                {{ $slot }}
                            @else
                                {{ $message }}
                            @endif
                    </p>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        {{ $cancelText }}
                    </button>
                    <button type="submit" class="btn {{ $confirmClass }} rounded-pill px-4">
                        {{ $confirmText }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>