"""
Tests for Takalo Vrai marketplace system
"""

import unittest
from takalo_vrai import Item, Exchange, Marketplace


class TestItem(unittest.TestCase):
    """Test cases for Item class."""
    
    def test_item_creation(self):
        """Test creating an item."""
        item = Item("Book", "A good book", "Alice")
        self.assertEqual(item.name, "Book")
        self.assertEqual(item.description, "A good book")
        self.assertEqual(item.owner, "Alice")
        self.assertEqual(item.category, "General")
        self.assertTrue(item.available)
    
    def test_item_with_category(self):
        """Test creating an item with a specific category."""
        item = Item("Laptop", "MacBook Pro", "Bob", "Electronics")
        self.assertEqual(item.category, "Electronics")
    
    def test_item_repr(self):
        """Test item string representation."""
        item = Item("Phone", "iPhone", "Charlie")
        repr_str = repr(item)
        self.assertIn("Phone", repr_str)
        self.assertIn("Charlie", repr_str)


class TestExchange(unittest.TestCase):
    """Test cases for Exchange class."""
    
    def setUp(self):
        """Set up test fixtures."""
        self.item1 = Item("Book", "Science book", "Alice")
        self.item2 = Item("Magazine", "Tech magazine", "Bob")
    
    def test_exchange_creation(self):
        """Test creating an exchange."""
        exchange = Exchange("Alice", self.item1, "Bob", self.item2)
        self.assertEqual(exchange.offerer, "Alice")
        self.assertEqual(exchange.receiver, "Bob")
        self.assertEqual(exchange.status, "Pending")
    
    def test_accept_exchange(self):
        """Test accepting an exchange."""
        exchange = Exchange("Alice", self.item1, "Bob", self.item2)
        result = exchange.accept()
        self.assertTrue(result)
        self.assertEqual(exchange.status, "Accepted")
        self.assertEqual(self.item1.owner, "Bob")
        self.assertEqual(self.item2.owner, "Alice")
        # Items should be marked as unavailable after exchange
        self.assertFalse(self.item1.available)
        self.assertFalse(self.item2.available)
    
    def test_reject_exchange(self):
        """Test rejecting an exchange."""
        exchange = Exchange("Alice", self.item1, "Bob", self.item2)
        result = exchange.reject()
        self.assertTrue(result)
        self.assertEqual(exchange.status, "Rejected")
    
    def test_cannot_accept_twice(self):
        """Test that an exchange cannot be accepted twice."""
        exchange = Exchange("Alice", self.item1, "Bob", self.item2)
        exchange.accept()
        result = exchange.accept()
        self.assertFalse(result)
    
    def test_cannot_reject_after_accept(self):
        """Test that an exchange cannot be rejected after accepting."""
        exchange = Exchange("Alice", self.item1, "Bob", self.item2)
        exchange.accept()
        result = exchange.reject()
        self.assertFalse(result)


class TestMarketplace(unittest.TestCase):
    """Test cases for Marketplace class."""
    
    def setUp(self):
        """Set up test fixtures."""
        self.marketplace = Marketplace()
        self.item1 = Item("Book", "Science book", "Alice", "Books")
        self.item2 = Item("Magazine", "Tech magazine", "Bob", "Books")
        self.item3 = Item("Laptop", "Dell XPS 13", "Charlie", "Electronics")
    
    def test_marketplace_creation(self):
        """Test creating a marketplace."""
        self.assertEqual(len(self.marketplace.items), 0)
        self.assertEqual(len(self.marketplace.exchanges), 0)
    
    def test_add_item(self):
        """Test adding an item to the marketplace."""
        result = self.marketplace.add_item(self.item1)
        self.assertTrue(result)
        self.assertEqual(len(self.marketplace.items), 1)
        self.assertIn(self.item1, self.marketplace.items)
    
    def test_remove_item(self):
        """Test removing an item from the marketplace."""
        self.marketplace.add_item(self.item1)
        result = self.marketplace.remove_item(self.item1)
        self.assertTrue(result)
        self.assertEqual(len(self.marketplace.items), 0)
    
    def test_remove_nonexistent_item(self):
        """Test removing an item that doesn't exist."""
        result = self.marketplace.remove_item(self.item1)
        self.assertFalse(result)
    
    def test_search_by_query(self):
        """Test searching items by query."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        self.marketplace.add_item(self.item3)
        
        results = self.marketplace.search_items(query="book")
        self.assertEqual(len(results), 1)  # Only "Book" item
    
    def test_search_by_category(self):
        """Test searching items by category."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        self.marketplace.add_item(self.item3)
        
        results = self.marketplace.search_items(category="Books")
        self.assertEqual(len(results), 2)
        
        results = self.marketplace.search_items(category="Electronics")
        self.assertEqual(len(results), 1)
    
    def test_search_by_owner(self):
        """Test searching items by owner."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        
        results = self.marketplace.search_items(owner="Alice")
        self.assertEqual(len(results), 1)
        self.assertEqual(results[0].owner, "Alice")
    
    def test_search_combined_filters(self):
        """Test searching with multiple filters."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        self.marketplace.add_item(self.item3)
        
        results = self.marketplace.search_items(query="book", category="Books")
        self.assertEqual(len(results), 1)  # Only "Book" in Books category
    
    def test_create_exchange(self):
        """Test creating an exchange."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        
        exchange = self.marketplace.create_exchange(
            "Alice", self.item1, "Bob", self.item2
        )
        
        self.assertIsNotNone(exchange)
        self.assertEqual(len(self.marketplace.exchanges), 1)
        self.assertEqual(exchange.status, "Pending")
    
    def test_create_exchange_with_unavailable_item(self):
        """Test creating an exchange with unavailable item."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        self.item1.available = False
        
        exchange = self.marketplace.create_exchange(
            "Alice", self.item1, "Bob", self.item2
        )
        
        self.assertIsNone(exchange)
    
    def test_create_exchange_wrong_owner(self):
        """Test creating an exchange with wrong owner."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        
        # Alice tries to offer Bob's item
        exchange = self.marketplace.create_exchange(
            "Alice", self.item2, "Bob", self.item1
        )
        
        self.assertIsNone(exchange)
    
    def test_get_user_items(self):
        """Test getting items owned by a user."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        self.marketplace.add_item(self.item3)
        
        alice_items = self.marketplace.get_user_items("Alice")
        self.assertEqual(len(alice_items), 1)
        self.assertEqual(alice_items[0].name, "Book")
    
    def test_get_pending_exchanges(self):
        """Test getting pending exchanges for a user."""
        self.marketplace.add_item(self.item1)
        self.marketplace.add_item(self.item2)
        
        exchange = self.marketplace.create_exchange(
            "Alice", self.item1, "Bob", self.item2
        )
        
        alice_exchanges = self.marketplace.get_pending_exchanges("Alice")
        bob_exchanges = self.marketplace.get_pending_exchanges("Bob")
        
        self.assertEqual(len(alice_exchanges), 1)
        self.assertEqual(len(bob_exchanges), 1)
        
        # After accepting, should not be in pending
        exchange.accept()
        alice_exchanges = self.marketplace.get_pending_exchanges("Alice")
        self.assertEqual(len(alice_exchanges), 0)


if __name__ == '__main__':
    unittest.main()
