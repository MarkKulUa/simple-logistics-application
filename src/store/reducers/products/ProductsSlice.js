import { createSlice, isAnyOf } from "@reduxjs/toolkit";
import {
  changeProductPerPage,
  createProduct,
  deleteProduct,
  fetchProducts,
  updateProduct,
} from "./ProductsActions";

const getErrorMessage = (payload) => {
  if (typeof payload === "string") {
    return payload;
  }
  return "An error occurred";
};

const initialState = {
  products: [],
  isLoading: false,
  error: "",
  productsPerPage: 5,
};

export const productSlice = createSlice({
  name: "products",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchProducts.fulfilled, (state, { payload }) => {
        state.products = payload || [];
      })
      .addCase(createProduct.fulfilled, (state, { payload }) => {
        state.products = [...state.products, payload];
      })
      .addCase(deleteProduct.fulfilled, (state, { payload }) => {
        state.products = state.products.filter((product) => product.id !== payload.id);
      })
      .addCase(updateProduct.fulfilled, (state, { payload }) => {
        state.products = state.products.map((product) =>
          product.id === payload.id ? payload : product
        );
      })
      .addCase(changeProductPerPage.fulfilled, (state, { payload }) => {
        state.productsPerPage = payload;
      })
      .addMatcher(
        isAnyOf(fetchProducts.pending, createProduct.pending, deleteProduct.pending),
        (state) => {
          state.isLoading = true;
          state.error = "";
        }
      )
      .addMatcher(
        isAnyOf(
          fetchProducts.fulfilled,
          createProduct.fulfilled,
          deleteProduct.fulfilled
        ),
        (state) => {
          state.isLoading = false;
          state.error = "";
        }
      )
      .addMatcher(
        isAnyOf(fetchProducts.rejected, createProduct.rejected, deleteProduct.rejected),
        (state, { payload }) => {
          state.isLoading = false;
          state.error = getErrorMessage(payload);
        }
      );
  },
});

export default productSlice.reducer;
