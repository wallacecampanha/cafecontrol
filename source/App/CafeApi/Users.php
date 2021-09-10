<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppInvoice;
use Source\Support\Thumb;
use Source\Support\Upload;

/**
 * Class Users
 * @package Source\App\CafeApi
 */
class Users extends CafeApi
{
    /**
     * Users constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list user data
     */
    public function index(): void
    {
        $user = $this->user->data();
        $user->photo = CONF_URL_BASE . "/" . CONF_UPLOAD_DIR . "/{$user->photo}";
        unset($user->password, $user->forget);

        $response["user"] = $user;
        $response["user"]->balance = (new AppInvoice())->balance($this->user);

        $this->back($response);
        return;
    }

    /**
     * @param array $data
     */
    public function update(array $data): void
    {
        $request = $this->requestLimit("usersUpdate", 5, 60);
        if (!$request) {
            return;
        }

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $genreList = ["female", "male", "other"];
        if (!empty($data["genre"]) && !in_array($data["genre"], $genreList)) {
            $this->call(
                400,
                "invalid_data",
                "Favor informe o gênero como feminino, masculino ou outro"
            )->back();
            return;
        }

        if (!empty($data["datebirth"])) {
            $check = \DateTime::createFromFormat("Y-m-d", $data["datebirth"]);
            if (!$check || $check->format("Y-m-d") != $data["datebirth"]) {
                $this->call(
                    400,
                    "invalid_data",
                    "Favor informe uma data de nascimento válida"
                )->back();
                return;
            }
        }

        $this->user->first_name = (!empty($data["first_name"]) ? $data["first_name"] : $this->user->first_name);
        $this->user->last_name = (!empty($data["last_name"]) ? $data["last_name"] : $this->user->last_name);
        $this->user->genre = (!empty($data["genre"]) ? $data["genre"] : $this->user->genre);
        $this->user->datebirth = (!empty($data["datebirth"]) ? $data["datebirth"] : $this->user->datebirth);
        $this->user->document = (!empty($data["document"]) ? $data["document"] : $this->user->document);

        if (!$this->user->save()) {
            $this->call(
                400,
                "invalid_data",
                $this->user->message()->getText()
            )->back();
            return;
        }

        $this->index();
    }

    /**
     * @throws \Exception
     */
    public function photo(): void
    {
        $request = $this->requestLimit("usersPhoto", 3, 60);
        if (!$request) {
            return;
        }

        $photo = (!empty($_FILES["photo"]) ? $_FILES["photo"] : null);
        if (!$photo) {
            $this->call(
                400,
                "invalid_data",
                "Envie uma imagem JPG ou PNG para atualizar a foto"
            )->back();
            return;
        }

        chdir("../");

        $upload = new Upload();
        $newPhoto = $upload->image($photo, $this->user->fullName(), 600);

        if (!$newPhoto) {
            $this->call(
                400,
                "invalid_data",
                $upload->message()->getText()
            )->back();
            return;
        }

        if ($this->user->photo() && $newPhoto != $this->user->photo) {
            unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$this->user->photo}");
            (new Thumb())->flush($this->user->photo);
        }

        $this->user->photo = $newPhoto;
        $this->user->save();
        $this->index();
    }
}