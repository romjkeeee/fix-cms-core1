<?php

namespace AltSolution\Admin\Auth;

class CodeTFA extends AbstractTFA
{
    /**
     * @var TwoFactorCodeInterface
     */
    private $codeService;

    public function __construct(UserStoreInterface $userStore, TwoFactorCodeInterface $codeService)
    {
        $this->userStore = $userStore;
        $this->codeService = $codeService;
    }

    public function sendVerification()
    {
        $code = $this->codeService->generate();
        $this->userStore->setCode($code);

        $user = $this->userStore->getUser();
        $this->codeService->send($user, $code);
    }

    public function validate($code)
    {
        $storedCode = $this->userStore->getCode();

        return hash_equals($storedCode, $code);
    }
}