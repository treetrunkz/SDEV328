arr = [];
var list = document.createElement('ul');
for(var i=1;i<100;i++){
    if(i % 3  === 0 && i % 5 === 0){
    arr.push("Hee Haw!")}
    else if(i % 3 === 0){
        arr.push("Hee!");
    }
    else if(i % 5 === 0){
        arr.push("Haw!");
    }
    else
        arr.push(i);
}
arr.forEach(function (arr) {
    var li = document.createElement('li');
    li.textContent = arr;
    list.appendChild(li);
});
var app = document.querySelector('#app');
app.appendChild(list);