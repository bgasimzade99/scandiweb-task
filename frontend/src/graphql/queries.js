import { gql } from '@apollo/client';

export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      id
      name
    }
  }
`;

export const GET_PRODUCTS = gql`
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      in_stock
      brand
      gallery
      prices {
        amount
        currency {
          label
          symbol
        }
      }
      attributes {
        id
        name
        type
        items {
          id
          value
          display_value
        }
      }
    }
  }
`;

export const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      in_stock
      brand
      description
      gallery
      prices {
        amount
        currency {
          label
          symbol
        }
      }
      attributes {
        id
        name
        type
        items {
          id
          value
          display_value
        }
      }
    }
  }
`;

export const PLACE_ORDER = gql`
  mutation PlaceOrder($order: PlaceOrderInput!) {
    placeOrder(order: $order)
  }
`;
