import ActionTracker from './ActionTracker'

let companyId = 123;
let taskId = 456;
let userId = 789;
let walletAddress = '0x45bff18af1e5eb4420913d9aa52aee78178e6345';
let privateKey = '45bff18af1e5eb4420913d9aa52aee78178e6345';

// click event track
let click = new ActionTracker(
    companyId,
    taskId,
    userId,
    {
        events: [{name: 'click'}]
    },
    walletAddress,
    privateKey
)
click.trackAction()
    .once('receipt', (receipt) => {
        console.log(receipt)
    })
    .catch(error => {
        console.log(error)
    })

// purchase event track
let purchase = new ActionTracker(
    companyId,
    taskId,
    userId,
    {
        events: [{name: 'purchase', amount: 1000}]
    },
    walletAddress,
    privateKey
)
purchase.trackAction()
    .once('receipt', (receipt) => {
        console.log(receipt)
    })
    .catch(error => {
        console.log(error)
    })