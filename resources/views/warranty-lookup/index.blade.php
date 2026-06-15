@extends('layouts.shop')

@section('title', 'Tra cứu bảo hành')

@section('content')
    <div class="container mt-5 mb-5">
        <section class="mx-auto" style="max-width: 1100px;">
            <div class="rounded-4 p-5 text-white shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);">
                <div class="d-flex flex-column gap-3 flex-lg-row justify-content-between align-items-lg-end">
                    <div>
                        <span class="badge bg-light text-primary">Tài khoản của bạn</span>
                        <h1 class="mt-3 fw-bold" style="font-size: 2rem;">Bảo hành của tôi</h1>
                        <p class="mt-3 mb-0" style="max-width: 44rem; line-height: 1.75; opacity: 0.92;">
                            Xem nhanh danh sách sản phẩm đã mua, thời gian bảo hành còn lại và gửi yêu cầu bảo hành ngay dưới từng sản phẩm khi có lỗi.
                        </p>
                    </div>
                    <div class="text-lg-end small" style="opacity: 0.9;">
                        <div>{{ auth()->user()->name }}</div>
                        <div>{{ auth()->user()->phone ?? auth()->user()->email }}</div>
                    </div>
                </div>
            </div>

            @if ($warranties->isEmpty())
                <div class="rounded-4 border border-dashed border-secondary bg-white px-6 py-5 shadow-sm text-center mt-4">
                    <h2 class="fw-bold mb-2">Bạn chưa có sản phẩm nào đủ điều kiện bảo hành</h2>
                    <p class="text-muted mb-0">Sau khi đơn hàng hoàn tất và hệ thống tạo serial bảo hành, danh sách sẽ hiển thị tại đây.</p>
                </div>
            @else
                <div class="mt-4 d-flex flex-column gap-3">
                    @foreach ($warranties as $warranty)
                        @php
                            $status = strtolower((string) $warranty->status);
                            $isExpired = $status === 'expired' || ($warranty->remaining_days !== null && $warranty->remaining_days < 0);
                            $claimStatus = $warranty->claim_status;
                            $claimBadgeClass = match ($claimStatus) {
                                'pending' => 'bg-warning text-dark',
                                'approved', 'received', 'in_progress' => 'bg-info text-dark',
                                'completed' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <article class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                    <div>
                                        <p class="text-uppercase text-muted small mb-2">Sản phẩm</p>
                                        <h2 class="fw-bold mb-2">{{ $warranty->product_display_name }}</h2>
                                        <div class="d-flex flex-wrap gap-2 align-items-center small text-muted">
                                            <span>Mã serial: <strong class="text-dark">{{ $warranty->serial_display }}</strong></span>
                                            <span>•</span>
                                            <span>Đơn hàng: <strong class="text-dark">{{ $warranty->orderItem?->order?->order_number ?? 'N/A' }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="text-lg-end">
                                        <span class="badge rounded-pill {{ $isExpired ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                                            {{ $isExpired ? 'Đã hết hạn' : 'Còn bảo hành' }}
                                        </span>
                                        @if ($warranty->claim_status)
                                            <div class="mt-2">
                                                <span class="badge rounded-pill {{ $claimBadgeClass }} px-3 py-2">
                                                    {{ $warranty->claim_status_label }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row g-3 mt-4">
                                    <div class="col-md-4">
                                        <div class="rounded-3 border bg-light p-3 h-100">
                                            <div class="text-uppercase text-muted small">Thời gian bảo hành</div>
                                            <div class="fw-semibold mt-2">{{ $warranty->activated_at_display ?? 'N/A' }} - {{ $warranty->expires_at_display ?? 'N/A' }}</div>
                                            <div class="text-muted small mt-1">
                                                @if ($isExpired)
                                                    Bảo hành của sản phẩm này đã kết thúc.
                                                @else
                                                    Còn {{ $warranty->remaining_days ?? 0 }} ngày.
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="rounded-3 border bg-light p-3 h-100">
                                            <div class="text-uppercase text-muted small">Ngày mua</div>
                                            <div class="fw-semibold mt-2">{{ $warranty->purchase_date_display ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="rounded-3 border bg-light p-3 h-100">
                                            <div class="text-uppercase text-muted small">Yêu cầu bảo hành</div>
                                            <div class="fw-semibold mt-2">{{ $warranty->claim_status_label ?? 'Chưa tạo yêu cầu' }}</div>
                                        </div>
                                    </div>
                                </div>

                                @if ($warranty->claim_status === 'rejected' && ! empty($warranty->claim_resolution_note))
                                    <div class="alert alert-danger mt-4 mb-0" role="alert">
                                        <div class="fw-semibold mb-1">Lý do từ chối bảo hành</div>
                                        <div>{{ $warranty->claim_resolution_note }}</div>
                                    </div>
                                @elseif (! empty($warranty->claim_technician_note))
                                    <div class="alert alert-info mt-4 mb-0" role="alert">
                                        <div class="fw-semibold mb-1">Cập nhật từ kỹ thuật</div>
                                        <div>{{ $warranty->claim_technician_note }}</div>
                                    </div>
                                @endif

                                <div class="mt-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                    <div class="small text-muted">
                                        {{ $warranty->can_request_claim ? 'Bạn có thể gửi yêu cầu bảo hành cho sản phẩm này.' : 'Yêu cầu bảo hành hiện đang được xử lý.' }}
                                    </div>
                                    @if ($isExpired)
                                        <button class="btn btn-outline-secondary" type="button" disabled>Hết hạn bảo hành</button>
                                    @elseif (! $warranty->can_request_claim)
                                        <button class="btn btn-outline-primary" type="button" disabled>
                                            {{ $warranty->claim_status_label ?? 'Đang xử lý' }}
                                        </button>
                                    @else
                                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#claim-form-{{ $warranty->id }}" aria-expanded="false" aria-controls="claim-form-{{ $warranty->id }}">
                                            Yêu cầu bảo hành
                                        </button>
                                    @endif
                                </div>

                                @if ($warranty->can_request_claim)
                                    <div class="collapse mt-4" id="claim-form-{{ $warranty->id }}">
                                        <form method="POST" action="{{ route('warranty-lookup.claims.store', $warranty->id) }}" enctype="multipart/form-data" class="rounded-4 border bg-white p-4">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold" for="issue-{{ $warranty->id }}">Mô tả lỗi</label>
                                                    <textarea
                                                        id="issue-{{ $warranty->id }}"
                                                        name="issue_description"
                                                        class="form-control"
                                                        rows="5"
                                                        placeholder="Mô tả chi tiết tình trạng lỗi, cách phát sinh và các dấu hiệu bạn quan sát được."
                                                        required
                                                    >{{ old('issue_description') }}</textarea>
                                                    <div class="form-text">Nên mô tả càng cụ thể càng tốt để admin tiếp nhận nhanh hơn.</div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold" for="attachments-{{ $warranty->id }}">Đính kèm ảnh/video</label>
                                                    <input id="attachments-{{ $warranty->id }}" type="file" name="attachments[]" class="form-control" multiple accept="image/*,video/*">
                                                    <div class="form-text">Hỗ trợ nhiều tệp ảnh hoặc video, tối đa 20MB mỗi tệp.</div>
                                                </div>
                                                <div class="col-12 d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#claim-form-{{ $warranty->id }}">Đóng</button>
                                                    <button type="submit" class="btn btn-dark">Gửi yêu cầu</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection