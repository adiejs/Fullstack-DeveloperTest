import { useEffect, useState } from 'react';
import { useAuth } from '../context/AuthContext';
import api from '../api/axios';
import ProductFormModal from '../components/ProductFormModal';
import ConfirmationModal from '../components/ConfirmationModal';

// Placeholder image
const PLACEHOLDER_IMAGE = 'https://placehold.co/100';

const ProductsPage = () => {
  const { logout } = useAuth();
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [currentProduct, setCurrentProduct] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');

  const [isConfirmModalOpen, setIsConfirmModalOpen] = useState(false);
  const [confirmModalMessage, setConfirmModalMessage] = useState('');
  const [confirmAction, setConfirmAction] = useState(null);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      const response = await api.get('/products');
      setProducts(response.data);
      setError('');
    } catch (err) {
      if (err.request) {
        setError('Could not connect to the server. Please check your API connection.');
      } else {
        setError('Failed to fetch products. Please try again.');
      }
      console.error('Failed to fetch products:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  useEffect(() => {
    const results = products.filter(product =>
      product.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      (product.description && product.description.toLowerCase().includes(searchQuery.toLowerCase()))
    );
    setFilteredProducts(results);
  }, [searchQuery, products]);

  const handleSaveProduct = async (formData) => {
    try {
      if (currentProduct) {
        await api.patch(`/products/${currentProduct.id}`, formData);
      } else {
        await api.post('/products', formData);
      }
      setIsModalOpen(false);
      setCurrentProduct(null);
      fetchProducts();
    } catch (err) {
      console.error('Failed to save product:', err);
      setError('Failed to save product. Please try again.');
    }
  };

  const handleConfirmAction = () => {
    if (confirmAction) {
      confirmAction();
    }
    setIsConfirmModalOpen(false);
    setConfirmAction(null);
  };

  const handleDeleteProduct = async (id) => {
    try {
      await api.delete(`/products/${id}`);
      fetchProducts();
    } catch (err) {
      console.error('Failed to delete product:', err);
      setError('Failed to delete product. Please try again.');
    }
  };

  const handleDeleteClick = (productId) => {
    setConfirmModalMessage('Apakah Anda yakin ingin menghapus produk ini?');
    setConfirmAction(() => () => handleDeleteProduct(productId));
    setIsConfirmModalOpen(true);
  };

  const handleLogoutClick = () => {
    setConfirmModalMessage('Apakah Anda yakin ingin keluar?');
    setConfirmAction(() => () => {
      logout();
      window.location.href = '/login';
    });
    setIsConfirmModalOpen(true);
  };

  const handleEditClick = (product) => {
    setCurrentProduct(product);
    setIsModalOpen(true);
  };

  const handleAddClick = () => {
    setCurrentProduct(null);
    setIsModalOpen(true);
  };

  if (loading) return <div className="min-h-screen flex items-center justify-center text-gray-400">Loading...</div>;
  if (error) return <div className="min-h-screen flex items-center justify-center text-red-400">{error}</div>;

  return (
    <div className="min-h-screen p-8">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-white">Daftar Produk</h1>
        <div className="flex space-x-4">
          <button onClick={handleAddClick} className="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
            Tambah Produk
          </button>
          <button onClick={handleLogoutClick} className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
            Logout
          </button>
        </div>
      </div>
      <div className="mb-6">
        <input
          type="text"
          placeholder="Cari produk..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
          className="w-full px-4 py-2 bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
        />
      </div>
      <div className="bg-gray-800 p-6 rounded-lg shadow-lg">
        {filteredProducts.length === 0 ? (
          <p className="text-center text-gray-400">Tidak ada produk yang ditemukan.</p>
        ) : (
          <ul className="space-y-4">
            {filteredProducts.map((product) => (
              <li key={product.id} className="bg-gray-700 p-4 rounded-md flex items-center space-x-4">
                <img
                  src={product.imageUrl || PLACEHOLDER_IMAGE}
                  alt={product.name}
                  className="w-24 h-24 object-cover rounded-md flex-shrink-0"
                />
                <div className="flex-grow">
                  <h3 className="text-lg font-semibold text-white">{product.name}</h3>
                  <p className="text-gray-400 line-clamp-2">{product.description}</p>
                  <p className="text-purple-400 font-medium">${product.price}</p>
                </div>
                <div className="flex-shrink-0 flex flex-col space-y-2">
                  <button onClick={() => handleEditClick(product)} className="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md">
                    Edit
                  </button>
                  <button onClick={() => handleDeleteClick(product.id)} className="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                    Hapus
                  </button>
                </div>
              </li>
            ))}
          </ul>
        )}
      </div>
      <ProductFormModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onSave={handleSaveProduct}
        product={currentProduct}
      />
      <ConfirmationModal
        isOpen={isConfirmModalOpen}
        onClose={() => setIsConfirmModalOpen(false)}
        onConfirm={handleConfirmAction}
        message={confirmModalMessage}
      />
    </div>
  );
};

export default ProductsPage;