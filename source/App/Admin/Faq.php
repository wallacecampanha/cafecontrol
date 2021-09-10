<?php

namespace Source\App\Admin;

use Source\Models\Faq\Channel;
use Source\Models\Faq\Question;
use Source\Support\Pager;

/**
 * Class Faq
 * @package Source\App\Admin
 */
class Faq extends Admin
{
    /**
     * Faq constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array|null $data
     */
    public function home(?array $data): void
    {
        $channels = (new Channel())->find();

        $pager = new Pager(url("/admin/faq/home/"));
        $pager->pager($channels->count(), 5, (!empty($data["page"]) ? $data["page"] : 1));

        $head = $this->seo->render(
            CONF_SITE_NAME . " | FAQs",
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/faqs/home", [
            "app" => "faq/home",
            "head" => $head,
            "channels" => $channels->order("channel")->limit($pager->limit())->offset($pager->offset())->fetch(true),
            "paginator" => $pager->render()
        ]);
    }

    /**
     * @param array|null $data
     */
    public function channel(?array $data): void
    {
        //create
        if (!empty($data["action"]) && $data["action"] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $channelCreate = new Channel();
            $channelCreate->channel = $data["channel"];
            $channelCreate->description = $data["description"];

            if (!$channelCreate->save()) {
                $json["message"] = $channelCreate->message()->render();
                echo json_encode($json);
                return;
            }

            $this->message->success("Canal cadastrado com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/faq/channel/{$channelCreate->id}")]);

            return;
        }

        //update
        if (!empty($data["action"]) && $data["action"] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $channelEdit = (new Channel())->findById($data["channel_id"]);

            if (!$channelEdit) {
                $this->message->error("Você tentou editar um canal que não existe ou foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $channelEdit->channel = $data["channel"];
            $channelEdit->description = $data["description"];

            if (!$channelEdit->save()) {
                $json["message"] = $channelEdit->message()->render();
                echo json_encode($json);
                return;
            }

            $json["message"] = $this->message->success("Canal atualizado com sucesso...")->render();
            echo json_encode($json);

            return;
        }

        //delete
        if (!empty($data["action"]) && $data["action"] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $channelDelete = (new Channel())->findById($data["channel_id"]);

            if (!$channelDelete) {
                $this->message->error("Você tentou remover um canal que não existe ou já foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $channelDelete->destroy();
            $this->message->success("Canal excluído com sucesso...")->flash();

            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        $channelEdit = null;
        if (!empty($data["channel_id"])) {
            $channelId = filter_var($data["channel_id"], FILTER_VALIDATE_INT);
            $channelEdit = (new Channel())->findById($channelId);
        }

        $head = $this->seo->render(
            CONF_SITE_NAME . " | " . ($channelEdit ? "FAQ: {$channelEdit->channel}" : "FAQ: Novo Canal"),
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/faqs/channel", [
            "app" => "faq/home",
            "head" => $head,
            "channel" => $channelEdit
        ]);
    }

    /**
     * @param array|null $data
     */
    public function question(?array $data): void
    {
        //create
        if (!empty($data["action"]) && $data["action"] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

            $questionCreate = new Question();
            $questionCreate->channel_id = $data["channel_id"];
            $questionCreate->question = $data["question"];
            $questionCreate->response = $data["response"];
            $questionCreate->order_by = $data["order_by"];

            if (!$questionCreate->save()) {
                $json["message"] = $questionCreate->message()->render();
                echo json_encode($json);
                return;
            }

            $this->message->success("Pergunta cadastrado com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/faq/question/{$questionCreate->channel_id}/{$questionCreate->id}")]);

            return;
        }

        //update
        if (!empty($data["action"]) && $data["action"] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $questionEdit = (new Question())->findById($data["question_id"]);

            if (!$questionEdit) {
                $this->message->error("Você tentou editar uma pergunta que não existe ou foi removida")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $questionEdit->channel_id = $data["channel_id"];
            $questionEdit->question = $data["question"];
            $questionEdit->response = $data["response"];
            $questionEdit->order_by = $data["order_by"];

            if (!$questionEdit->save()) {
                $json["message"] = $questionEdit->message()->render();
                echo json_encode($json);
                return;
            }

            $json["message"] = $this->message->success("Pergunta atualizada com sucesso...")->render();
            echo json_encode($json);
            return;
        }

        //delete
        if (!empty($data["action"]) && $data["action"] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
            $questionDelete = (new Question())->findById($data["question_id"]);

            if (!$questionDelete) {
                $this->message->error("Você tentou remover uma pergunta que não existe")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $questionDelete->destroy();
            $this->message->success("Pergunta excluída com sucesso...")->flash();

            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        $channel = (new Channel())->findById($data["channel_id"]);
        $question = null;

        if (!$channel) {
            $this->message->warning("Você tentou gerenciar perguntas de um canal que não existe")->flash();
            redirect("/admin/faq/home");
        }

        if (!empty($data["question_id"])) {
            $questionId = filter_var($data["question_id"], FILTER_VALIDATE_INT);
            $question = (new Question())->findById($questionId);
        }


        $head = $this->seo->render(
            CONF_SITE_NAME . " | FAQ: Perguntas em {$channel->channel}",
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/faqs/question", [
            "app" => "faq/home",
            "head" => $head,
            "channel" => $channel,
            "question" => $question
        ]);
    }
}