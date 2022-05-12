// Cria um arquivo sql com alguns jogos extraidos da "https://api.rawg.io"

let sql = 'INSERT INTO `game` (`id`, `name`, `image`) VALUES\n';

var textFile = null,
makeTextFile = function (text) {
    var data = new Blob([text], {type: "text/plain;charset=utf-8"});
    if (textFile !== null) {
        window.URL.revokeObjectURL(textFile);
    }
    textFile = window.URL.createObjectURL(data);
    // abre uma nova aba com o sql
    console.log('link do sql gerado: "'+textFile+'"');
    window.open(textFile, '_blank');
};

// {max} -> quantas requests serão feitas a partir de {page}
// Cada request retorna pelo menos 20 resultados
function getGame2(max, page){
    fetch('https://api.rawg.io/api/games?key=c542e67aec3a4340908f9de9e86038af&genres=59&page='+page)
    .then((res) => {
        return res.json().then((json) => {
            if(json.detail){
                if(page < max){
                    getGame2(max, page+1);
                }else{
                    makeTextFile(sql);
                }
            }else{
                json.results.forEach((item, index) => {
                    sql+= `(${item.id}, "${item.name}", "${item.background_image}"),\n`;
                    console.log('Gerado página '+page+' de '+max);
                });
                
                if(page < max){
                    getGame2(max, page+1);
                }else{
                    makeTextFile(sql);
                }
            }
        });
    })
}

getGame2(5, 1);