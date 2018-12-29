import ActionTrackerContractAbi from './ActionsTrackerAbi'
import Web3Utils from 'web3-utils'
import Web3js from 'web3'
import EthTx from 'ethereumjs-tx'

/**
 * Action tracker
 */
export default class ActionTracker {

    /**
     *
     * @param companyId
     * @param taskId
     * @param userId
     * @param events
     * @param walletAddress
     * @param privateKey
     */
    constructor(companyId, taskId, userId, events, walletAddress, privateKey) {
        this.companyId = companyId
        this.taskId = taskId
        this.userId = userId
        this.events = events
        this.walletAddress = walletAddress
        this.privateKey = privateKey
        this.networkProvider = 'https://rinkeby.infura.io'
        this.contractAddress = '0x30c6424a23ea4a5573a49c7c09c10e94b6e9ef3b'
        this.gwei = 20
        this.gas = 200000
        this.web3 = new Web3js(new Web3js.providers.HttpProvider(this.networkProvider))
    }

    /**
     * Track action
     *
     * @returns {Promise<*>}
     */
    async trackAction() {
        try {
            let txData = await this.getTransactionData(),
                transaction = new EthTx(txData)

            transaction.sign(new Buffer(this.privateKey, 'hex'))
            transaction = '0x' + transaction.serialize().toString('hex')

            return this.web3.eth.sendSignedTransaction(transaction)
        } catch (e) {
            console.log(e)
        }
    }

    /**
     * Get transaction data
     *
     * @returns {Promise<{from: *, nonce: *, gasPrice: *, gasLimit: *, to: string, data: *}>}
     */
    async getTransactionData() {

        let nonce = await this.web3.eth.getTransactionCount(this.walletAddress)
        let contractInstance = new this.web3.eth.Contract(
            ActionTrackerContractAbi,
            this.contractAddress
        )

        let events = []
        for (let i = 0; i < this.events.events.length; i++) {
            events.push(
                Web3Utils.toHex(
                    JSON.stringify(this.events.events[i])
                )
            )
        }

        return {
            from: this.walletAddress,
            nonce: Web3Utils.toHex(nonce),
            gasPrice: Web3Utils.toHex(this.gwei * 1000000000),
            gasLimit: Web3Utils.toHex(this.gas),
            to: this.contractAddress,
            data: contractInstance.methods.trackAction(
                this.companyId,
                this.userId,
                this.taskId,
                events,
            ).encodeABI(),
        }
    }
}