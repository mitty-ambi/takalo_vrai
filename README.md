# Takalo Vrai

**Takalo Vrai** (meaning "Real Exchange" in Malagasy/French) is a simple marketplace system that allows users to exchange items with each other.

## Features

- **Item Management**: Add and manage items in the marketplace
- **Search Functionality**: Search for items by name, description, category, or owner
- **Exchange System**: Create, accept, or reject exchange offers between users
- **User Management**: Track items owned by users and manage exchanges

## Installation

No external dependencies required! This project uses only Python standard library.

```bash
# Clone the repository
git clone https://github.com/mitty-ambi/takalo_vrai.git
cd takalo_vrai
```

## Usage

### Basic Example

```python
from takalo_vrai import Item, Marketplace

# Create a marketplace
marketplace = Marketplace()

# Add items
book = Item("Python Book", "Learn Python Programming", "Alice", "Books")
laptop = Item("Laptop", "Dell XPS 13", "Bob", "Electronics")

marketplace.add_item(book)
marketplace.add_item(laptop)

# Search for items
results = marketplace.search_items(query="python")

# Create an exchange
exchange = marketplace.create_exchange("Alice", book, "Bob", laptop)

# Accept the exchange
if exchange:
    exchange.accept()
```

### Running the Example

```bash
python example.py
```

## Running Tests

```bash
python -m unittest test_takalo_vrai.py
```

Or with verbose output:

```bash
python -m unittest test_takalo_vrai.py -v
```

## API Reference

### Classes

#### `Item`
Represents an item available for exchange.

**Parameters:**
- `name` (str): Name of the item
- `description` (str): Description of the item
- `owner` (str): Owner of the item
- `category` (str, optional): Category of the item (default: "General")

#### `Exchange`
Represents an exchange offer between two users.

**Methods:**
- `accept()`: Accept the exchange and transfer ownership
- `reject()`: Reject the exchange

#### `Marketplace`
Main marketplace for managing items and exchanges.

**Methods:**
- `add_item(item)`: Add an item to the marketplace
- `remove_item(item)`: Remove an item from the marketplace
- `search_items(query, category, owner)`: Search for items
- `create_exchange(offerer, offerer_item, receiver, receiver_item)`: Create an exchange offer
- `get_user_items(username)`: Get all items owned by a user
- `get_pending_exchanges(username)`: Get pending exchanges for a user

## License

This project is open source and available for educational purposes.
