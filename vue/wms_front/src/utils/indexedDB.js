export function indexedDBFn(data){
    let db; // 全局的indexedDB数据库实例。
    let indexedDB = window.indexedDB || window.webkitIndexedDB || window.mozIndexedDB || window.msIndexedDB;
    if (!indexedDB) {
      console.log('你的浏览器不支持IndexedDB');
    };
    let request = window.indexedDB.open('myDatabase', 3);
    request.onupgradeneeded = function(event) {
        db = event.target.result;
        let store;
        if (!db.objectStoreNames.contains('newUsers')) {
            store = db.createObjectStore('newUsers', {keyPath: 'id', autoIncrement: true });
        };
        console.log(store);
        // 创建索引
        store.createIndex('userIndex', 'userName', { unique: false });
        store.createIndex('userEmail', 'email', { unique: true });
    };
    request.onsuccess = function(event) {
        db = event.target.result;
        let transaction = db.transaction(['newUsers'], 'readwrite');
        let objStore = transaction.objectStore('newUsers'); 
        // 使用流标 openCursor
        objStore.openCursor().onsuccess = function(e) {
            let cursor = e.target.result;
            if(!cursor){ // 初始化 添加所有库位
                console.log('初始化')
                for (let i = 0; i < data.length; i++) {                  
                    objStore.put(data[i]);
                };
            }else{       // 初始化以后如果再次获取所有库位则更新
                console.log('不是初始化')
                // for (let i = 1; i <= data.length; i++) { 
                //     let getmess = objStore.get(i);
                //     let getmessAll = objStore.getAll();                    
                //     getmessAll.onsuccess = function(e) {  
                //         let message = e.target.result;//这是数据
                //         console.log(message)
                //         //在这查到数据后 重新赋值 或者 加个 整条信息的参数mes，message = mes 整条替换
                //         message['stock_no'] = data[i-1]['stock_no'];
                //         objStore.put(message)//把更换后的数据 更新                                           
                //     };
                // }; 
            };
        };    
    };        
}
export function deleteDB(dbname) {
    let indexedDB = window.indexedDB || window.webkitIndexedDB || window.mozIndexedDB || window.msIndexedDB;
    indexedDB.deleteDatabase(dbname);
    console.log(dbname + '数据库已删除')
}