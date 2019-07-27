<!DOCTYPE html>
<header>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <img id="logo" src="SavvyLogo.png" width="300">
    <script src="anime.js"></script>
<script>
    anime({
        targets: '#logo',
        rotate: {
            value: 360,
            duration: 1000,
            easing: 'easeInOutSine'
        },
        scale: {
            value: 1.25,
            duration: 1000,
            delay: 800,
            easing: 'easeInOutQuart'
        }
    });
</script>
</header>

<body>
    <h1>BE SAVVY AND SAVE</h1>
    <form>
        <input type="number" name="income" placeholder="income" min="1" class="quantity" step=".01">
        <br>
        <button type="submit" name="submit" value="submit">Calculate</button>
    </form>
    
    <?php
        if (isset($_GET['submit'])) {
            $result = $_GET['income'];
            echo "<h1>Needs is equal to 50% of your income</h1>";
            ($need = $result * (50/100));
            echo "<h2> $$need </h2>";
            echo "<h1>Wants is equal to 30% of your income</h1>";
            ($want = $result * (30/100));
            echo "<h2> $$want </h2>";
            echo "<h1> Savings is equal to 20% of your income</h1>";
            ($savings = $result * (20/100));
            echo "<h2> $$savings </h2>";
        }
        
    ?>
    
</body>
