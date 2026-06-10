<?php
session_start();

if (!isset($_COOKIE['cart'])) {
    $cart = array();
    setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
} else {
    $cart = json_decode($_COOKIE['cart'], true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'add') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $product_name = isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : '';
        $product_price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($product_id > 0 && $quantity > 0) {
            $item_key = 'product_' . $product_id;

            if (isset($cart[$item_key])) {
                $cart[$item_key]['quantity'] += $quantity;
            } else {
                $cart[$item_key] = array(
                    'product_id' => $product_id,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'quantity' => $quantity
                );
            }

            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);

            $_SESSION['message'] = 'Product added to cart successfully!';
        }
    }

    // Remove item from cart
    elseif ($action == 'remove') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $item_key = 'product_' . $product_id;

        if (isset($cart[$item_key])) {
            unset($cart[$item_key]);
            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);

            $_SESSION['message'] = 'Product removed from cart!';
        }
    }

    // Update item quantity
    elseif ($action == 'update') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $item_key = 'product_' . $product_id;

        if (isset($cart[$item_key])) {
            if ($quantity <= 0) {
                unset($cart[$item_key]);
                $_SESSION['message'] = 'Product removed from cart!';
            } else {
                $cart[$item_key]['quantity'] = $quantity;
                $_SESSION['message'] = 'Cart updated successfully!';
            }

            setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
            $_COOKIE['cart'] = json_encode($cart);
        }
    }

    // Clear entire cart
    elseif ($action == 'clear') {
        $cart = array();
        setcookie('cart', json_encode($cart), time() + (86400 * 30), "/");
        $_COOKIE['cart'] = json_encode($cart);

        $_SESSION['message'] = 'Cart cleared!';
    }
}

// Get cart function
function getCart()
{
    if (isset($_COOKIE['cart'])) {
        return json_decode($_COOKIE['cart'], true);
    }
    return array();
}

// Get cart total
function getCartTotal()
{
    $cart = getCart();
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['product_price'] * $item['quantity'];
    }

    return $total;
}

// Get cart item count
function getCartItemCount()
{
    $cart = getCart();
    $count = 0;

    foreach ($cart as $item) {
        $count += $item['quantity'];
    }

    return $count;
}

// Display cart items (for debugging/testing)
if (isset($_GET['view'])) {
    echo '<pre>';
    echo 'Cart Contents:<br>';
    var_dump(getCart());
    echo '<br>Total: $' . number_format(getCartTotal(), 2);
    echo '<br>Item Count: ' . getCartItemCount();
    echo '</pre>';
}
?>