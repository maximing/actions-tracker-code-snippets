<?php

use kornrunner\Ethereum\Transaction;
use Web3\Contract;
use Web3\Eth;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;

/**
 * Class ActionTracker
 */
class ActionTracker
{
    const NETWORK_PROVIDER = '';
    const CONTRACT_ADDRESS = '';
    const GAS = 200000;
    const GWEI = 20;

    /**
     * @var Eth
     */
    private $web3Instance;

    /**
     * @var Contract
     */
    private $contractInstance;

    /**
     * @var int
     */
    private $companyId;

    /**
     * @var int
     */
    private $taskId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $events;

    /**
     * @var string
     */
    private $walletAddress;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * ActionTracker constructor.
     * @param int $companyId
     * @param int $taskId
     * @param int $userId
     * @param array $events
     * @param string $walletAddress
     * @param string $privateKey
     */
    public function __construct(
        int $companyId,
        int $taskId,
        int $userId,
        array $events,
        string $walletAddress,
        string $privateKey
    ) {
        $this->companyId = $companyId;
        $this->taskId = $taskId;
        $this->userId = $userId;
        $this->events = $events;
        $this->walletAddress = $walletAddress;
        $this->privateKey = $privateKey;
    }

    /**
     * Track action
     */
    public function trackAction()
    {
        $web3 = $this->getWeb3Instance();
        $web3->getTransactionCount(
            $this->getWalletAddress(),
            function ($error, $result) use ($web3) {
                $nonce = gmp_intval($result->value);
                $transaction = new Transaction(
                    $nonce == 0 ? '0' : Utils::toHex($nonce, true),
                    Utils::toHex(self::GWEI * 1000000000, true),
                    Utils::toHex(self::GAS, true),
                    self::CONTRACT_ADDRESS,
                    '',
                    $this->getTransactionData()
                );
                $signedTransaction = $transaction->getRaw(
                    $this->getPrivateKey()
                );
                $web3->sendRawTransaction("0x{$signedTransaction}", function ($error, $result) {});
            }
        );
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     */
    public function setCompanyId(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param array $events
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return mixed
     */
    public function getWalletAddress()
    {
        return $this->walletAddress;
    }

    /**
     * @param mixed $walletAddress
     */
    public function setWalletAddress($walletAddress)
    {
        $this->walletAddress = $walletAddress;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param mixed $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Get web3 instance
     *
     * @return \Web3\Eth
     */
    private function getWeb3Instance(): Eth
    {
        if (!empty($this->web3Instance)) {
            return $this->web3Instance;
        } else {
            $web3 = new Web3(
                new HttpProvider(
                    new HttpRequestManager(self::NETWORK_PROVIDER, 10)
                )
            );

            $instance = $web3->getEth();
            $this->web3Instance = $instance;

            return $instance;
        }
    }

    /**
     * Get ActionTracker contract instance
     *
     * @return Contract
     */
    private function getContractInstance(): Contract
    {
        if (!empty($this->contractInstance)) {
            return $this->contractInstance;
        } else {
            $contract = new Contract(
                self::NETWORK_PROVIDER,
                file_get_contents('ActionsTrackerAbi.json')
            );

            $instance = $contract->at(self::CONTRACT_ADDRESS);
            $this->contractInstance = $instance;

            return $instance;
        }

    }

    /**
     * Get transaction data
     *
     * @return string
     */
    private function getTransactionData(): string
    {
        $events = [];
        array_walk($this->getEvents(), function (array $event) use ($events) {
            $events[] = Utils::toHex(json_encode($event));
        });

        return $this->getContractInstance()
            ->getData(
                'trackAction',
                $this->getCompanyId(),
                $this->getUserId(),
                $this->getTaskId(),
                $events
            );
    }
}
