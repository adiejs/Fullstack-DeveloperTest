import { useState, useEffect, useRef } from 'react';
import api from '../api/axios';

const ProductFormModal = ({ isOpen, onClose, onSave, product }) => {
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    imageUrl: '',
  });
  const [selectedFile, setSelectedFile] = useState(null);
  const [uploading, setUploading] = useState(false);
  const [formErrors, setFormErrors] = useState({});
  const fileInputRef = useRef(null);

  useEffect(() => {
    if (product) {
      setFormData({
        name: product.name,
        description: product.description || '',
        price: product.price,
        imageUrl: product.imageUrl || '',
      });
    } else {
      setFormData({
        name: '',
        description: '',
        price: '',
        imageUrl: '',
      });
    }
    setFormErrors({});
    setSelectedFile(null);
    if (fileInputRef.current) {
        fileInputRef.current.value = "";
    }
  }, [product, isOpen]);

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: name === 'price' ? parseFloat(value) || '' : value,
    }));
  };

  const handleFileChange = (e) => {
    setSelectedFile(e.target.files[0]);
  };

  const handleUploadImage = async () => {
    if (!selectedFile) return null;

    setUploading(true);
    const data = new FormData();
    data.append('file', selectedFile);

    try {
      const response = await api.post('/products/upload-image', data, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      setUploading(false);
      return response.data.url;
    } catch (error) {
      setUploading(false);
      console.error('Error uploading image:', error);
      setFormErrors((prev) => ({ ...prev, imageUrl: 'Gagal mengunggah gambar.' }));
      return null;
    }
  };

  const validateForm = () => {
    const errors = {};
    if (!formData.name.trim()) {
      errors.name = 'Nama produk wajib diisi.';
    }
    if (!formData.price || isNaN(formData.price) || formData.price <= 0) {
      errors.price = 'Harga wajib diisi dengan angka positif.';
    }
    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (validateForm()) {
      let finalData = { ...formData };
      
      if (selectedFile) {
        const newImageUrl = await handleUploadImage();
        if (!newImageUrl) return;
        finalData.imageUrl = newImageUrl;
      } else if (product && !product.imageUrl) {
        delete finalData.imageUrl;
      } else if (!product && !formData.imageUrl) {

        delete finalData.imageUrl;
      }

      onSave(finalData);
    }
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 className="text-2xl font-bold mb-6 text-center text-white">
          {product ? 'Edit Product' : 'Add Product'}
        </h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-400 mb-2" htmlFor="name">Name</label>
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              className="w-full px-4 py-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
              required
            />
            {formErrors.name && <p className="text-red-400 text-sm mt-1">{formErrors.name}</p>}
          </div>
          <div className="mb-4">
            <label className="block text-gray-400 mb-2" htmlFor="description">Description</label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleChange}
              rows="3"
              className="w-full px-4 py-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
            ></textarea>
          </div>
          <div className="mb-4">
            <label className="block text-gray-400 mb-2" htmlFor="image">Product Image</label>
            <input
              type="file"
              name="image"
              accept="image/*"
              onChange={handleFileChange}
              ref={fileInputRef}
              className="w-full text-white bg-gray-700 rounded-md file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-500 file:text-white hover:file:bg-purple-600"
            />
            {uploading && <p className="text-purple-400 text-sm mt-1">Uploading image...</p>}
            {formErrors.imageUrl && <p className="text-red-400 text-sm mt-1">{formErrors.imageUrl}</p>}
            {formData.imageUrl && !selectedFile && (
              <div className="mt-2 text-gray-400">
                Current Image: <a href={formData.imageUrl} target="_blank" rel="noopener noreferrer" className="text-purple-400 hover:underline">View</a>
              </div>
            )}
          </div>
          <div className="mb-6">
            <label className="block text-gray-400 mb-2" htmlFor="price">Price</label>
            <input
              type="number"
              name="price"
              value={formData.price}
              onChange={handleChange}
              className="w-full px-4 py-2 bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-600"
              step="0.01"
              required
            />
            {formErrors.price && <p className="text-red-400 text-sm mt-1">{formErrors.price}</p>}
          </div>
          <div className="flex justify-end space-x-4">
            <button
              type="button"
              onClick={onClose}
              className="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md transition duration-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-md transition duration-300"
              disabled={uploading}
            >
              {uploading ? 'Uploading...' : (product ? 'Save Changes' : 'Add Product')}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default ProductFormModal;