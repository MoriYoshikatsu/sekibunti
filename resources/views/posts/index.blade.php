<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>リーマン積分Webアプリ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px 0;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .secondary-button {
            background-color: #6c757d;
            margin-top: 10px;
        }
        .secondary-button:hover {
            background-color: #5a6268;
        }
        .graph-wrap {
        width: 260px;
        }
        .graph {
        width: 100%;
        height: 260px;
        } */

    </style>
    <script type="text/javascript" charset="UTF-8" src="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraphcore.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/jsxgraph/distrib/jsxgraph.css" />
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" id="MathJax-script" async></script>
</head>
<body>
    <div id="jxgbox" class="jxgbox" style="width:2000px; height:500px;"></div>
    <script>
        // グラフ描画
        var board = JXG.JSXGraph.initBoard('jxgbox', {axis:true, boundingbox: [-8, 4, 8, -4], showNavigation:true});
        var s = board.create('slider',[[1,3],[5,3],[10,100,1000]],{name:'n',snapWidth:1});
        var a = board.create('slider',[[1,2],[5,2],[-10,-5,0]],{name:'start'});
        var b = board.create('slider',[[1,1],[5,1],[0,5,10]],{name:'end'});
        // var f = function(x){ return Math.sin(x); };
        board.create('text', [1, 3, '<button onclick="updateGraph()">Update graph</button>']);

        // Create an input element at position [1,4].
        var input = board.create('input', [0, 1, 'sin(x)*x', 'f(x)='], {cssStyle: 'width: 100px'});
        var f = board.jc.snippet(input.Value(), true, 'x', false);
        var graph = board.create('functiongraph',[f,
                function() {
                var c = new JXG.Coords(JXG.COORDS_BY_SCREEN,[0,0],board);
                return c.usrCoords[1];
                },
                function() {
                var c = new JXG.Coords(JXG.COORDS_BY_SCREEN,[board.canvasWidth,0],board);
                return c.usrCoords[1];
                }
            ]);
       
        var plot = board.create('functiongraph',[f,function(){return a.Value();}, function(){return b.Value();}]);

        var os = board.create('riemannsum',[f,
            function(){ return s.Value();}, function(){ return "left";},
            function(){return a.Value();},
            function(){return b.Value();}
            ],
            {fillColor:'#ffff00', fillOpacity:1.3});

        board.create('text',[-6,-3,function(){ return 'Sum='+(JXG.Math.Numerics.riemannsum(f,s.Value(),'left',a.Value(),b.Value())).toFixed(4); }]);

        var updateGraph = function() {
            f = board.jc.snippet(input.Value(), true, 'x', false);
            // グラフ再描画
            graph.Y = f;
            graph.updateCurve();
            // 関数グラフ更新
            plot.Y = f;
            plot.updateCurve();
            // リーマン和を削除して再生成
            board.removeObject(os);
            os = board.create('riemannsum', [
                f,
                function(){ return s.Value(); },
                function(){ return "left"; },
                function(){ return a.Value(); },
                function(){ return b.Value(); }
            ], {
                fillColor:'#ffff00',
                fillOpacity:1.3
            });

            board.update();
        };

    </script>

    <!-- <div class="container">
        <h1 style="text-align: center;">リーマン積分Webアプリ</h1>
        <div class="graph-wrap">
            <div id="plot" class="graph"></div>
            <div class="math"></div>
        </div>
        <div class="form-group">
            <label for="function">被積分関数</label>
            <input type="text" id="function" placeholder="例: x^2 + 3">
        </div>
        <div class="form-group">
            <label>積分区間</label>
            <input type="text" id="from" placeholder="から (例: 0)">
            <input type="text" id="to" placeholder="まで (例: 1)" style="margin-top: 10px;">
        </div>
        <button>完了</button>
        <button class="secondary-button">みんなの投稿を見る</button>
    </div> -->
    <!-- <script>
        let board = JXG.JSXGraph.initBoard('plot', {
            boundingbox: [ -0.1, 1.1, 1.1, -0.1],  // 領域の座標[左、上、右、下]
            axis: true,  // 軸を表示する
            showNavigation: true,  // ナビゲーションボタンを表示しない
            showCopyright: false    // コピーライト文字列を表示しない
        });

        const text_css = 'font-family: "Times New Roman", Times, "serif"; font-style: italic';
        board.create('text', [1.05, 0.08, 't'],
            { fontSize: 16, cssStyle: text_css });
        board.create('text', [0.05, 1.05, 'y'],
            { fontSize: 16, cssStyle: text_css });
        function bezier(t) {
            return t * t * (3 - 2 * t);
        }

        let graph = board.create('functiongraph', [bezier, 0, 10]);

        let slider = board.create('slider', [[0.2, 0.4], [0.8, 0.4], [1, 2, 4] ], {name: 'p'});
        slider.on('drag', function(e) {
            console.log('p=' + this.Value());
        });

        function clearGraph() {
            if (graph) { // 今表示されている曲線があれば消す
                board.removeObject(graph);
                graph = null;
            }
            if (slider) { // スライダーが表示されていれば消す
                board.removeObject(slider);
                slider = null;
            }
        }

        MathJax.typesetClear([$('.math').get(0)]);
        const math = '$$f_{p}(t)=\\frac{t^p}{t^p+(1-t)^p}$$';
        $('.math').html(math);
        MathJax.typeset();

    </script> -->
</body>
</html>