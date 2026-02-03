</div>
<script>
    var options = {
        chart: {
            type: 'line',
            height: 300
        },
        series: [{
            name: 'Bezoekers',
            data: [10, 25, 18, 40, 55, 30, 70,220]
        }],
        xaxis: {
            categories: ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo']
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>

</body>
</html>

