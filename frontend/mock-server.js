/**
 * Mock GraphQL server - çalıştırmak için: node mock-server.js
 * PHP yüklü değilse uygulamayı test etmek için kullanın.
 */
import { createServer } from 'http';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __dirname = dirname(fileURLToPath(import.meta.url));
const dataPath = join(__dirname, '../src/Controller/data.json');
let rawData;
try {
  rawData = JSON.parse(readFileSync(dataPath, 'utf8'));
} catch {
  rawData = { data: { categories: [], products: [] } };
}

const data = rawData.data;
const categories = (data.categories || []).map((c, i) => ({ id: String(i + 1), name: c.name }));

function mapProduct(p) {
  const attrs = (p.attributes || []).map((a) => ({
    id: a.id || a.name,
    name: a.name,
    type: a.type || 'text',
    items: (a.items || []).map((i) => ({
      id: i.id || i.value,
      value: i.value,
      display_value: i.displayValue || i.display_value || i.value,
    })),
  }));
  return {
    id: p.id,
    name: p.name,
    in_stock: p.inStock ?? true,
    brand: p.brand || '',
    description: p.description || '',
    gallery: p.gallery || [],
    prices: (p.prices || []).map((pr) => ({
      amount: pr.amount,
      currency: pr.currency || { label: 'USD', symbol: '$' },
    })),
    attributes: attrs,
  };
}

const products = (data.products || []).map(mapProduct);

function handleGraphQL(body) {
  const { query, variables } = JSON.parse(body || '{}');
  if (!query) return { errors: [{ message: 'Query required' }] };

  if (query.includes('GetCategories')) {
    return { data: { categories } };
  }

  if (query.includes('GetProducts')) {
    const cat = variables?.category || 'all';
    let list;
    if (cat === 'all') {
      list = products;
    } else {
      list = (rawData.data?.products || [])
        .filter((x) => x.category === cat)
        .map(mapProduct);
    }
    return { data: { products: list } };
  }

  if (query.includes('GetProduct')) {
    const id = variables?.id;
    const p = (rawData.data?.products || []).find((x) => x.id === id);
    return { data: { product: p ? mapProduct(p) : null } };
  }

  if (query.includes('PlaceOrder')) {
    const order = variables?.order;
    return { data: { placeOrder: 1 } };
  }

  return { errors: [{ message: 'Unknown query' }] };
}

const server = createServer((req, res) => {
  if (req.method === 'OPTIONS') {
    res.writeHead(204, {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'POST, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type',
    });
    res.end();
    return;
  }
  if (req.method === 'POST' && req.url === '/graphql') {
    let body = '';
    req.on('data', (chunk) => (body += chunk));
    req.on('end', () => {
      const result = handleGraphQL(body);
      res.writeHead(200, {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
      });
      res.end(JSON.stringify(result));
    });
    return;
  }
  res.writeHead(404);
  res.end();
});

const PORT = 8000;
server.listen(PORT, () => {
  console.log(`Mock GraphQL server: http://localhost:${PORT}`);
  console.log('Frontend: npm run dev (başka terminalde)');
});
