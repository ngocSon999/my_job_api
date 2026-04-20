<?php

namespace App\Services\Interaction;

use App\Models\Interaction;
use App\Models\Post;
use App\Models\User;
use App\Models\UserCreditScore;
use App\Repositories\Interaction\InteractionRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


/**
 * @property InteractionRepositoryInterface $repository
 */
class InteractionService extends BaseService
{
    protected PostRepositoryInterface $postRepository;
    public function __construct(
        InteractionRepositoryInterface $interactionRepository,
        PostRepositoryInterface $postRepository
    )
    {
        parent::__construct($interactionRepository);
        $this->postRepository = $postRepository;
    }

    /**
     * @throws Exception
     */
    public function createInteraction(array $data)
    {
        $post = $this->postRepository->findById($data['post_id']);
        if (!$post) {
            throw new Exception("Bài đăng không tồn tại.");
        }

        $currentUserId = Auth::id();

        if ($post->user_id === $currentUserId) {
            throw new Exception("Bạn không thể tự gửi yêu cầu cho bài đăng của chính mình.");
        }

        $existing = $this->repository->findExisting($currentUserId, $post->id, $post->user_id);

        if ($existing) {
            if ($existing->status === 'pending') {
                throw new Exception("Yêu cầu này đang trong trạng thái chờ, bạn không cần gửi thêm.");
            }
            if ($existing->status === 'accepted') {
                throw new Exception("Bạn và người này đã kết nối thành công trước đó.");
            }
        }

        DB::beginTransaction();
        try {
            $data['employer_id']  = $currentUserId;
            $data['candidate_id'] = $post->user_id;
            $data['status']       = 'pending';

            $interaction = $this->repository->create($data);

            DB::commit();

            return $interaction;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception("Lỗi khi tạo kết nối: " . $e->getMessage());
        }
    }

    public function getMyNotifications(Request $request): ?LengthAwarePaginator
    {
        $perPage = $request->per_page ?? null;

        return $this->repository->getAllInteractions(Auth::id(), $perPage);

    }

    /**
     * @throws Exception
     */
    public function respondInteraction($id, $status)
    {
        if (!in_array($status, ['accepted', 'rejected'])) {
            throw new Exception("Trạng thái không hợp lệ.");
        }

        $interaction = $this->repository->findById($id);

        if (!$interaction || $interaction->candidate_id !== Auth::id()) {
            throw new Exception("Yêu cầu không hợp lệ hoặc bạn không có quyền.", 403);
        }

        if ($interaction->status !== 'pending') {
            throw new Exception("Yêu cầu này đã được xử lý.");
        }

        DB::beginTransaction();
        try {
            $updatedInteraction = $this->repository->update($id, ['status' => $status]);

            $scoreChange = ($status === 'accepted') ? 5 : -3;
            $reason = ($status === 'accepted') ? "Đồng ý kết nối" : "Từ chối kết nối";

            $this->updateUserCreditScore(
                $interaction->candidate_id,
                $scoreChange,
                $reason,
                $id,
                Interaction::class
            );

            DB::commit();

            return $updatedInteraction;
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception("Lỗi xử lý phản hồi: " . $e->getMessage());
        }
    }

    /**
     *  cập nhật điểm và lưu lịch sử
     */
    protected function updateUserCreditScore($userId, $change, $reason, $sourceId, $sourceType): void
    {
        $user = User::findOrFail($userId);
        $newTotal = $user->credit_score + $change;
        UserCreditScore::create([
            'user_id' => $userId,
            'score_change' => $change,
            'current_total_score' => $newTotal,
            'reason' => $reason,
            'source_id' => $sourceId,
            'source_type' => $sourceType,
        ]);

        $user->update(['credit_score' => $newTotal]);
    }
}
