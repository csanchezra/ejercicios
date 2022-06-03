var m1 = 0;
var m2 = 0;
var n = 0;
var ini_points = 0;
var player_win = Math.floor(Math.random() * 2) + 1;

document.getElementById('inputfile')
    .addEventListener('change', function ()
    {
        let fr = new FileReader();
        fr.onload = function ()
        {
            let lines = fr.result.split('\n');
            validar_lineas(lines);

            for (let line = 0; line < lines.length; line++)
            {
                if (line == 0)
                {
                    n = lines[line];
                    if (!isNum(n)) WriteToFile();
                    if (lines - 1 != n) WriteToFile();
                    if (n > 10000) WriteToFile();
                }
                else
                {
                    let porciones = lines[line].split(' ');

                    if (!isNum(porciones[0]) || !isNum(porciones[1])) WriteToFile();
                    m1 = porciones[0];
                    m2 = porciones[1];


                    var absolut = m1 - m2;

                    if (Math.abs(absolut) > ini_points)
                    {
                        if (absolut > 0)
                        {
                            ini_points = Math.abs(absolut);
                            player_win = "1";
                        }
                        else if (absolut < 0)
                        {
                            ini_points = Math.abs(absolut);
                            player_win = "2";
                        }

                    }
                }

            }

        }

        fr.readAsText(this.files[0]);

        console.info(player_win)
    })

function validar_lineas(lineas)
{
    if (lineas > 10001 || lineas < 2)
    {
        WriteToFile()
    }
}


function WriteToFile()
{
    var file = document.getElementById("inputfile");

    if (file.files.length == 0)
    {
        alert("Ingrese un archivo");
    } else
    {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(player_win + " " + ini_points));
        element.setAttribute('download', "result_js.txt");

        element.style.display = 'none';
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
        document.getElementById("inputfile").value = "";
        m1 = 0;
        m2 = 0;
        n = 0;
        ini_points = 0;
        player_win = Math.floor(Math.random() * 2) + 1;
    }
    // return false;
}