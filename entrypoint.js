import 'babel-polyfill'
import ActionTracker from './ActionTracker'

let companyId = 10;
let taskId = 456;
let userId = 789;
let walletAddress = '0xF735D16458160a982eaB18503C72c696c665353'
let privateKey = 'cea068816277159557a0dbd0750b276f9c9c946f20078d88f89a2b5'

let init = async () => {
    // click event track
    let click = new ActionTracker(
        companyId,
        taskId,
        userId,
        {
            events: [{nm: 'click'}]
        },
        walletAddress,
        privateKey
    )
    await click.trackAction()

    // purchase event track
    let purchase = new ActionTracker(
        companyId,
        taskId,
        userId,
        {
            events: [{nm: 'purchase', am: 1000}]
        },
        walletAddress,
        privateKey
    )
    await purchase.trackAction()
}

init()