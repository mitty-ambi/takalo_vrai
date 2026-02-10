#!/usr/bin/env python3
"""
Example usage of Takalo Vrai marketplace system
"""

from takalo_vrai import Item, Marketplace


def main():
    """Demonstrate the marketplace functionality."""
    print("=" * 60)
    print("Welcome to Takalo Vrai - Real Exchange Marketplace")
    print("=" * 60)
    print()
    
    # Create a marketplace
    marketplace = Marketplace()
    
    # Add some items
    print("Adding items to the marketplace...")
    items_to_add = [
        Item("Python Book", "Learn Python Programming", "Alice", "Books"),
        Item("JavaScript Book", "Modern JavaScript Guide", "Bob", "Books"),
        Item("Laptop", "Dell XPS 13", "Charlie", "Electronics"),
        Item("Headphones", "Sony WH-1000XM4", "Alice", "Electronics"),
        Item("Coffee Maker", "Espresso Machine", "Bob", "Appliances"),
    ]
    
    for item in items_to_add:
        marketplace.add_item(item)
        print(f"  Added: {item.name} (Owner: {item.owner}, Category: {item.category})")
    
    print()
    
    # Search for items
    print("Searching for 'book' items...")
    results = marketplace.search_items(query="book")
    for item in results:
        print(f"  - {item.name} by {item.owner}")
    print()
    
    # Search by category
    print("Searching for Electronics...")
    results = marketplace.search_items(category="Electronics")
    for item in results:
        print(f"  - {item.name} by {item.owner}")
    print()
    
    # Get user's items
    print("Alice's items:")
    alice_items = marketplace.get_user_items("Alice")
    for item in alice_items:
        print(f"  - {item.name} ({item.category})")
    print()
    
    # Create an exchange
    print("Creating an exchange offer...")
    print("  Alice offers: Python Book")
    print("  Bob offers: JavaScript Book")
    
    alice_item = next(item for item in items_to_add if item.name == "Python Book")
    bob_item = next(item for item in items_to_add if item.name == "JavaScript Book")
    
    exchange = marketplace.create_exchange("Alice", alice_item, "Bob", bob_item)
    
    if exchange:
        print(f"  Exchange created! Status: {exchange.status}")
        print()
        
        # Accept the exchange
        print("Bob accepts the exchange...")
        if exchange.accept():
            print(f"  Exchange accepted! Status: {exchange.status}")
            print(f"  Python Book now owned by: {alice_item.owner}")
            print(f"  JavaScript Book now owned by: {bob_item.owner}")
        print()
    
    # Show pending exchanges
    print("Checking pending exchanges for Charlie...")
    pending = marketplace.get_pending_exchanges("Charlie")
    print(f"  Charlie has {len(pending)} pending exchange(s)")
    print()
    
    print("=" * 60)
    print("Demo completed successfully!")
    print("=" * 60)


if __name__ == '__main__':
    main()
