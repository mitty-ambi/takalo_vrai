"""
Takalo Vrai - A Real Exchange/Marketplace System

This module implements a simple marketplace where users can:
- List items for exchange
- Search for items
- Make exchange offers
- Complete exchanges
"""


class Item:
    """Represents an item available for exchange."""
    
    def __init__(self, name, description, owner, category="General"):
        """
        Initialize an item.
        
        Args:
            name (str): Name of the item
            description (str): Description of the item
            owner (str): Owner of the item
            category (str): Category of the item (default: "General")
        """
        self.name = name
        self.description = description
        self.owner = owner
        self.category = category
        self.available = True
    
    def __repr__(self):
        status = "Available" if self.available else "Not Available"
        return f"Item(name='{self.name}', owner='{self.owner}', status={status})"


class Exchange:
    """Represents an exchange offer between two users."""
    
    def __init__(self, offerer, offerer_item, receiver, receiver_item):
        """
        Initialize an exchange.
        
        Args:
            offerer (str): Person offering the exchange
            offerer_item (Item): Item being offered
            receiver (str): Person receiving the offer
            receiver_item (Item): Item requested in exchange
        """
        self.offerer = offerer
        self.offerer_item = offerer_item
        self.receiver = receiver
        self.receiver_item = receiver_item
        self.status = "Pending"
    
    def accept(self):
        """Accept the exchange."""
        if self.status == "Pending":
            self.status = "Accepted"
            self.offerer_item.owner = self.receiver
            self.receiver_item.owner = self.offerer
            return True
        return False
    
    def reject(self):
        """Reject the exchange."""
        if self.status == "Pending":
            self.status = "Rejected"
            return True
        return False
    
    def __repr__(self):
        return (f"Exchange(offerer='{self.offerer}', "
                f"receiver='{self.receiver}', status='{self.status}')")


class Marketplace:
    """Main marketplace for managing items and exchanges."""
    
    def __init__(self):
        """Initialize the marketplace."""
        self.items = []
        self.exchanges = []
    
    def add_item(self, item):
        """
        Add an item to the marketplace.
        
        Args:
            item (Item): Item to add
        
        Returns:
            bool: True if item was added successfully
        """
        self.items.append(item)
        return True
    
    def remove_item(self, item):
        """
        Remove an item from the marketplace.
        
        Args:
            item (Item): Item to remove
        
        Returns:
            bool: True if item was removed successfully
        """
        if item in self.items:
            self.items.remove(item)
            return True
        return False
    
    def search_items(self, query=None, category=None, owner=None):
        """
        Search for items in the marketplace.
        
        Args:
            query (str): Search query for name or description
            category (str): Filter by category
            owner (str): Filter by owner
        
        Returns:
            list: List of matching items
        """
        results = self.items.copy()
        
        if query:
            query_lower = query.lower()
            results = [item for item in results 
                      if query_lower in item.name.lower() 
                      or query_lower in item.description.lower()]
        
        if category:
            results = [item for item in results if item.category == category]
        
        if owner:
            results = [item for item in results if item.owner == owner]
        
        return results
    
    def create_exchange(self, offerer, offerer_item, receiver, receiver_item):
        """
        Create an exchange offer.
        
        Args:
            offerer (str): Person offering the exchange
            offerer_item (Item): Item being offered
            receiver (str): Person receiving the offer
            receiver_item (Item): Item requested in exchange
        
        Returns:
            Exchange: The created exchange or None if items not available
        """
        if not offerer_item.available or not receiver_item.available:
            return None
        
        if offerer_item.owner != offerer or receiver_item.owner != receiver:
            return None
        
        exchange = Exchange(offerer, offerer_item, receiver, receiver_item)
        self.exchanges.append(exchange)
        return exchange
    
    def get_user_items(self, username):
        """
        Get all items owned by a user.
        
        Args:
            username (str): Username to search for
        
        Returns:
            list: List of items owned by the user
        """
        return [item for item in self.items if item.owner == username]
    
    def get_pending_exchanges(self, username):
        """
        Get all pending exchanges for a user.
        
        Args:
            username (str): Username to search for
        
        Returns:
            list: List of pending exchanges involving the user
        """
        return [exchange for exchange in self.exchanges 
                if (exchange.receiver == username or exchange.offerer == username) 
                and exchange.status == "Pending"]
